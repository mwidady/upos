<?php

namespace app\controllers;

use app\models\Items;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Transactions;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\Response;

class SiteController extends Controller
{
    
    public $enableCsrfValidation = false; 
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login','login2','error','forgotpassword'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
  }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user = User::findOne(Yii::$app->user->id);
        $branchId = $user->branch_id;
        $db = Yii::$app->db;

        $summary = $db->createCommand(
            "SELECT COUNT(*) AS invoice_count,
                    COALESCE(SUM(amount), 0) AS sales_total,
                    COALESCE(SUM(CASE WHEN status = 'PAID' THEN amount ELSE 0 END), 0) AS paid_total,
                    COALESCE(SUM(CASE WHEN status <> 'PAID' THEN amount ELSE 0 END), 0) AS outstanding_total
             FROM invoices
             WHERE branch_id = :branchId",
            [':branchId' => $branchId]
        )->queryOne();

        $summary['item_count'] = (int) Items::find()->where(['branch_id' => $branchId])->count();
        $summary['user_count'] = (int) User::find()->where(['branch_id' => $branchId])->count();
        $summary['customer_count'] = (int) $db->createCommand(
            'SELECT COUNT(*) FROM customers WHERE branch_id = :branchId',
            [':branchId' => $branchId]
        )->queryScalar();
        $summary['low_stock_count'] = (int) $db->createCommand(
            'SELECT COUNT(*) FROM items i
             LEFT JOIN quantity_total q ON q.item_id = i.id
             WHERE i.branch_id = :branchId AND COALESCE(q.quantity_t, 0) <= 5',
            [':branchId' => $branchId]
        )->queryScalar();

        $latestDate = $db->createCommand(
            'SELECT MAX(DATE(created_at)) FROM invoices WHERE branch_id = :branchId',
            [':branchId' => $branchId]
        )->queryScalar();
        $periodEnd = new \DateTimeImmutable($latestDate ?: 'today');
        $periodStart = $periodEnd->modify('-6 days');

        $salesRows = $db->createCommand(
            'SELECT DATE(created_at) AS sale_day, COALESCE(SUM(amount), 0) AS total
             FROM invoices
             WHERE branch_id = :branchId AND DATE(created_at) BETWEEN :periodStart AND :periodEnd
             GROUP BY DATE(created_at)',
            [
                ':branchId' => $branchId,
                ':periodStart' => $periodStart->format('Y-m-d'),
                ':periodEnd' => $periodEnd->format('Y-m-d'),
            ]
        )->queryAll();
        $salesByDay = array_column($salesRows, 'total', 'sale_day');
        $dailySales = [];
        for ($day = $periodStart; $day <= $periodEnd; $day = $day->modify('+1 day')) {
            $key = $day->format('Y-m-d');
            $dailySales[] = [
                'label' => $day->format('D'),
                'date' => $day->format('d M'),
                'value' => (float) ($salesByDay[$key] ?? 0),
            ];
        }

        $statusRows = $db->createCommand(
            'SELECT status, COUNT(*) AS total FROM invoices
             WHERE branch_id = :branchId GROUP BY status ORDER BY total DESC',
            [':branchId' => $branchId]
        )->queryAll();
        $invoiceStatuses = array_map(function ($row) {
            return ['label' => $row['status'] ?: 'Unknown', 'value' => (int) $row['total']];
        }, $statusRows);

        $topProducts = $db->createCommand(
            'SELECT i.name, COALESCE(SUM(t.quantity), 0) AS quantity
             FROM transactions t
             INNER JOIN items i ON i.id = t.item_id
             INNER JOIN invoices inv ON inv.id = t.invoice_id
             WHERE inv.branch_id = :branchId
             GROUP BY i.id, i.name
             ORDER BY quantity DESC
             LIMIT 5',
            [':branchId' => $branchId]
        )->queryAll();

        $activityRows = $db->createCommand(
            "SELECT CASE
                        WHEN HOUR(COALESCE(t.ts_time, t.trans_date)) < 10 THEN 'Opening'
                        WHEN HOUR(COALESCE(t.ts_time, t.trans_date)) < 13 THEN 'Morning'
                        WHEN HOUR(COALESCE(t.ts_time, t.trans_date)) < 17 THEN 'Afternoon'
                        ELSE 'Evening'
                    END AS period_name,
                    COUNT(*) AS total
             FROM transactions t
             INNER JOIN invoices inv ON inv.id = t.invoice_id
             WHERE inv.branch_id = :branchId
             GROUP BY period_name",
            [':branchId' => $branchId]
        )->queryAll();
        $activityMap = array_column($activityRows, 'total', 'period_name');
        $activity = [];
        foreach (['Opening', 'Morning', 'Afternoon', 'Evening'] as $period) {
            $activity[] = ['label' => $period, 'value' => (int) ($activityMap[$period] ?? 0)];
        }

        $recentInvoices = $db->createCommand(
            'SELECT inv.invoiceno, inv.amount, inv.status, inv.created_at,
                    CONCAT(c.fname, " ", c.lname) AS customer_name
             FROM invoices inv
             LEFT JOIN customers c ON c.id = inv.customer_id
             WHERE inv.branch_id = :branchId
             ORDER BY inv.created_at DESC, inv.id DESC
             LIMIT 4',
            [':branchId' => $branchId]
        )->queryAll();

        return $this->render('index', [
            'summary' => $summary,
            'dailySales' => $dailySales,
            'invoiceStatuses' => $invoiceStatuses,
            'topProducts' => $topProducts,
            'activity' => $activity,
            'recentInvoices' => $recentInvoices,
            'periodEnd' => $periodEnd,
        ]);
    }

    //sales
    public function actionSales()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }


        return $this->render('sales');
    }
    public function actionTransactions()
    {
    }
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'mainlogin';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect('index');
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // return $this->goBack();
            return $this->redirect('index');
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
    
     public function actionLogin2()
    {
       Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new LoginForm();
        $model->load(Yii::$app->request->post(), '');

       if ($model->login()) {
            return [
                'success' => true,
                'user' => [
                    'id' => Yii::$app->user->id,
                    'username' => Yii::$app->user->identity->username,
                ],
            ];
        }

        return [
            'success' => false,
            'errors' => $model->getErrors(),
        ];
    }
    
    

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        if (!Yii::$app->user->isGuest) {
            \app\models\AuditTrail::record(
                \app\models\AuditTrail::ACTION_LOGOUT,
                'User',
                'id=' . Yii::$app->user->id,
                [],
                ['username' => Yii::$app->user->identity->username],
                'User logged out'
            );
        }
        Yii::$app->user->logout();
        return $this->redirect('login');
        //return $this->goHome();
        
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
public function actionForgotpassword(){
        $model = new LoginForm();

        if($model->load(Yii::$app->request->post())){
            $count_us = User::find()->where(['email' => $model->email])->count();
            if($count_us > 0){
                $connection = \Yii::$app->db;
                $connection->createCommand("UPDATE users SET password = md5('12345') email='".$model->email."'")->execute();
                \Yii::$app->getSession()->setFlash('success','Please login using password sent to your email');
            }else{
                \Yii::$app->getSession()->setFlash('error','Wrong email Address');
            }
        }
         return $this->redirect('login');
}
    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

      /**
     * Displays Help page.
     *
     * @return string
     */
    public function actionHelp()
    {
        return $this->render('help');
    }
}
