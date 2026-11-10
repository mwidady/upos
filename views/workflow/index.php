<?php

use app\models\Workflow;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\WorkflowSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Workflows';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workflow-index">


    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'branch.name',
            'workflow_code',
            [
                'class' => ActionColumn::className(),
                'header' => 'Action',
                'urlCreator' => function ($action, Workflow $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
