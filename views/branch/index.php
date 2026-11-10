<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BranchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Branches';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="branch-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Branch', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'station.name',
            'createdBy.username',
            'description:ntext',
            //'address',
            //'location',
            //'tel',
            //'fax',
            'email:email',
            'created_at',
            //'updated_at',

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
