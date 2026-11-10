<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model) {
            return [
                'style' => (int) $model->status === \app\models\User::STATUS_DISABLED ? 'background-color:#f2dede;' : '',
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>'Name',
                'attribute'=>'first_name',
                'value' => function($data){
                    return $data->first_name." ".$data->last_name;
                }
            ],
            'username',
            'email:email',
            'branch.name',
            'branch.station.name',
            [
                'attribute' => 'status',
                'filter' => \app\models\User::statusOptions(),
                'format' => 'raw',
                'value' => function ($model) {
                    return (int) $model->status === \app\models\User::STATUS_ENABLED
                        ? '<span class="label label-success">Enabled</span>'
                        : '<span class="label label-danger">Disabled</span>';
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
            'header' => 'Action',
            'template' => '{view} {update} {setpassword} {togglestatus}',
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
                'setpassword' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-lock"></span>', ['setpassword', 'id' => $model->id],
                    [
                        'title' => 'Change Password',
                        'class'=>'btn btn-warning btn-xs',
                    ]);
                },
                'togglestatus' => function ($url, $model) {
                    $isEnabled = (int) $model->status === \app\models\User::STATUS_ENABLED;
                    return Html::a(
                        $isEnabled ? '<span class="glyphicon glyphicon-ban-circle"></span>' : '<span class="glyphicon glyphicon-ok-circle"></span>',
                        ['togglestatus', 'id' => $model->id],
                        [
                            'title' => $isEnabled ? 'Disable User' : 'Enable User',
                            'class' => $isEnabled ? 'btn btn-danger btn-xs' : 'btn btn-success btn-xs',
                            'data' => [
                                'confirm' => $isEnabled ? 'Disable this user account?' : 'Enable this user account?',
                                'method' => 'post',
                            ],
                        ]
                    );
                },
              
            ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
