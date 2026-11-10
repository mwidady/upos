<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BatchesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Batches';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
<div class="batches-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Batches', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'start_at',
            'end_at',
            'generatedBy.username',
            'station.name',
            'created_at',

            ['class' => 'yii\grid\ActionColumn',
            'header' => 'Action',
            'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, 
                        [
                            'title' => 'View',
                             'class'=>'btn btn-primary btn-xs',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, 
                        [
                            'title' => 'Update',
                             'class'=>'btn btn-info btn-xs',
                        ]);
                    },
                  
                ],
        ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
    </div>
