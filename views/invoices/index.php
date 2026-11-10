<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="invoices-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'invoiceno',
            'amount',
            'generatedBy.username',
            // 'value' => function($model) {
            //     return $model->quantity * $model->price ;
            // },
            [
            'attribute' => 'status',
            'filter' =>['PAID'=>'PAID','CANCELLED'=>'CANCELLED','NOT PAID'=>'NOT PAID'],
            'format'=>'raw',
            'value' => function($model){
                if($model->status == 'PAID'){
                   return "<span class='text-success'>".$model->status."</span>";
                  
                }
               else if($model->status == 'PAID PARTIAL'){
                     return "<span class='text-warning'>".$model->status."</span>";
                }
                else if($model->status == 'CANCELLED'){
                    return "<span class='text-danger'>".$model->status."</span>";
               }
                else{
                    return "<span class='text-primary'>".$model->status."</span>";   
                }
            }
            ],
       
            //'paid_time',
            //'paid_through',
            //'payment_type',
            'created_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'template' => '{view}{viewreceipt}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="">Invoice</span>', $url, 
                        [
                            'title' => 'View',
                            'style' =>'margin-right:10px;',
                             'class'=>'btn btn-primary btn-xs',
                        ]);
                    },
                    'viewreceipt' =>function ($url, $model) {
                        return $model->status=="PAID"? Html::a('<span class="">Receipt</span>', $url, 
                        [
                            'title' => 'View Receipt',
                             'class'=>'btn btn-success btn-xs',
                        ]):"";
                    }
                
                    //Html::a('<span>Details</span>', ['items/recdetails?rec_time='.$items['ts_time']], ['class'=>'btn btn-primary btn-mini'])
                ],
        
        ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
</div>
