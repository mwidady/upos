<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Expenditures;
use yii\filters\AccessControl;
use app\models\ExpendituresSearch;
use app\models\ExpenseType;
use yii\web\NotFoundHttpException;

/**
 * ExpendituresController implements the CRUD actions for Expenditures model.
 */
class ExpendituresController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login','error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Expenditures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExpendituresSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Expenditures model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Expenditures model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Expenditures();

        if ($model->load(Yii::$app->request->post())) {
            $branchId= User::findOne(Yii::$app->user->id)->branch_id;
            $expenseType = ExpenseType::findOne([
                'id' => $model->expense_type_id,
                'branch_id' => $branchId,
                'status' => ExpenseType::STATUS_ENABLED,
            ]);
            if ($expenseType === null) {
                $model->addError('expense_type_id', 'Please select an enabled expense type.');
                return $this->render('create', [
                    'model' => $model,
                    'expenseTypes' => ExpenseType::optionsForBranch($branchId),
                ]);
            }
            $model->user_id =  Yii::$app->user->id;
            $model->edate =  date('Y-m-d');
            $model->branch_id =  $branchId;
            $model->etype = $expenseType->name;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Expense created successfully.');
                return $this->redirect('index');
            }
            return $this->render('create', [
                'model' => $model,
                'expenseTypes' => ExpenseType::optionsForBranch($branchId, $model->expense_type_id),
            ]);
        } else {
            $branchId = User::findOne(Yii::$app->user->id)->branch_id;
            return $this->render('create', [
                'model' => $model,
                'expenseTypes' => ExpenseType::optionsForBranch($branchId),
            ]);
        }
    }

    /**
     * Updates an existing Expenditures model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $currentTypeId = $model->expense_type_id;
        if ($model->load(Yii::$app->request->post())) {
            $expenseType = ExpenseType::find()
                ->where(['id' => $model->expense_type_id, 'branch_id' => $branchId])
                ->andWhere(['or', ['status' => ExpenseType::STATUS_ENABLED], ['id' => $currentTypeId]])
                ->one();
            if ($expenseType === null) {
                $model->addError('expense_type_id', 'Please select a valid expense type.');
            } else {
                $model->etype = $expenseType->name;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Expense updated successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'expenseTypes' => ExpenseType::optionsForBranch($branchId, $model->expense_type_id),
        ]);
    }

    /**
     * Deletes an existing Expenditures model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Expenditures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Expenditures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $query = Expenditures::find()->where(['id' => $id]);
        \app\components\BranchAccess::apply($query, 'branch_id', $branchId);
        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

