<?php

namespace app\controllers;

use Yii;
use app\models\AuditTrail;
use app\models\AuditTrailSearch;
use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AuditTrailsController extends Controller
{
    public function behaviors()
    {
        return ['access' => ['class' => AccessControl::className(), 'rules' => [['allow' => true, 'roles' => ['Manage Role']]]]];
    }

    public function actionIndex()
    {
        $searchModel = new AuditTrailSearch();
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $searchModel->search(Yii::$app->request->queryParams)]);
    }

    public function actionView($id) { return $this->render('view', ['model' => $this->findModel($id)]); }

    protected function findModel($id)
    {
        $identity = User::findOne(Yii::$app->user->id);
        $query = AuditTrail::find()->where(['id' => $id]);
        \app\components\BranchAccess::apply($query, 'branch_id', $identity->branch_id);
        $model = $query->one();
        if ($model !== null) { return $model; }
        throw new NotFoundHttpException('The requested audit entry does not exist.');
    }
}
