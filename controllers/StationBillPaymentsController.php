<?php

namespace app\controllers;

use Yii;
use app\models\StationBillPayment;
use app\models\StationBillPaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class StationBillPaymentsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'notify' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new StationBillPaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new StationBillPayment();
        $model->paid_date = date('Y-m-d');

        if ($model->load(Yii::$app->request->post())) {
            $model->created_by = Yii::$app->user->id;
            $model->created_at = date('Y-m-d H:i:s');
            $model->notification_status = StationBillPayment::STATUS_PENDING;
            $model->save(false);

            Yii::$app->session->setFlash('success', 'Station bill payment created successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionNotify($id)
    {
        $model = $this->findModel($id);
        $model->notification_status = StationBillPayment::STATUS_NOTIFIED;
        $model->notified_at = date('Y-m-d H:i:s');
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Station bill payment marked as notified.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = StationBillPayment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
