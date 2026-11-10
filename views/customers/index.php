<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <p>
        <?= Html::a('Create Customers', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model) {
            $url = Url::toRoute(['/invoices/index', 'InvoicesSearch[customer_id]' => $model->id]);

            return [
                'style' => $model->hasUnpaidInvoices() ? 'background-color:#f2dede; cursor:pointer;' : 'cursor:pointer;',
                'onclick' => 'window.location.href="' . $url . '"',
                'title' => 'View related invoices',
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>'Name',
                'attribute'=>'lname',
                'value' => function($data){
                    return $data->displayName;
                }
            ],
            'phone',
            'company',
            [
                'label' => 'Invoice Status',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->hasUnpaidInvoices()
                        ? '<span class="label label-danger">NOT PAID INVOICE</span>'
                        : '<span class="label label-success">CLEAR</span>';
                }
            ],
            // 'position',
            // 'id',
            /*[
                'attribute' => 'clogo',
                'format' => 'html',
                'value' => function ($data) {
                    return Html::img(Yii::getAlias('@web/')."uploads/".$data['clogo'], ['width' => '70px']);
                },
            ],
*/
            // 'address',
            'email:email',

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
<?php Pjax::end(); ?></div>
