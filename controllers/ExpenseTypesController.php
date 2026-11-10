<?php

namespace app\controllers;

use Yii;
use app\models\ExpenseType;
use app\models\ExpenseTypeSearch;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ExpenseTypesController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => ['toggle-status' => ['POST']],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [['allow' => true, 'roles' => ['Manage Expenditure']]],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ExpenseTypeSearch();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ]);
    }

    public function actionCreate()
    {
        $model = new ExpenseType();
        $model->branch_id = User::findOne(Yii::$app->user->id)->branch_id;
        $model->status = ExpenseType::STATUS_ENABLED;
        $model->created_by = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Expense type created successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Expense type updated successfully.');
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == ExpenseType::STATUS_ENABLED
            ? ExpenseType::STATUS_DISABLED
            : ExpenseType::STATUS_ENABLED;
        $model->updated_at = date('Y-m-d H:i:s');
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Expense type status updated.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $model = ExpenseType::findOne(['id' => $id, 'branch_id' => $branchId]);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested expense type does not exist.');
    }
}

