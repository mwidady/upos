<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="stations-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Stations', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model) {
            return [
                'style' => (int) $model->status === \app\models\Stations::STATUS_DISABLED ? 'background-color:#f2dede;' : '',
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'name',
            'description:ntext',
            'address',
            'location',
            'tel',
            //'fax',
            'email:email',
            [
                'attribute' => 'status',
                'filter' => \app\models\Stations::statusOptions(),
                'format' => 'raw',
                'value' => function ($model) {
                    return (int) $model->status === \app\models\Stations::STATUS_ENABLED
                        ? '<span class="label label-success">Enabled</span>'
                        : '<span class="label label-danger">Disabled</span>';
                }
            ],
            //'created_at',

            ['class' => 'yii\grid\ActionColumn',
            'header' => 'Action',
            'template' => '{view} {update} {togglestatus}',
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
                    'togglestatus' => function ($url, $model) {
                        $isEnabled = (int) $model->status === \app\models\Stations::STATUS_ENABLED;
                        return Html::a(
                            $isEnabled ? '<span class="glyphicon glyphicon-ban-circle"></span>' : '<span class="glyphicon glyphicon-ok-circle"></span>',
                            ['togglestatus', 'id' => $model->id],
                            [
                                'title' => $isEnabled ? 'Disable Station' : 'Enable Station',
                                'class' => $isEnabled ? 'btn btn-danger btn-xs' : 'btn btn-success btn-xs',
                                'data' => [
                                    'confirm' => $isEnabled ? 'Disable this station and restrict all its users from login?' : 'Enable this station and allow its users to login again?',
                                    'method' => 'post',
                                ],
                            ]
                        );
                    },
                  
                ],
        ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
</div>
