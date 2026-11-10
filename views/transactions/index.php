<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use app\components\AppGridView as GridView;
use yii\console\Request;
//use kartik\widgets\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TransactionsSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transactions';
$this->params['breadcrumbs'][] = $this->title;

if(isset(Yii::$app->request->queryParams['TransactionsSerach']['trans_date'])){
    $searcdate = Yii::$app->request->queryParams['TransactionsSerach']['trans_date'];
}else{
    $searcdate = "";
}

//Yii::$app->request->queryParams['TransactionsSerach']['trans_date'];
    $gridColumns =[
        ['class' => 'kartik\grid\SerialColumn'],
        [
            'attribute' => 'item_id',
            'value'=>'item.name',
            'label' => 'Item Name',
            'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        ],
        [
            'attribute'=>'quantity',
            'contentOptions' => ['style' => 'width:100px; white-space: normal;'],
        ],
        [
            'attribute' => 'price',
            'format'=>['decimal',2],
            'label' => 'Amount',

        ],
        [
            'label' => 'Total Amount',
            'format'=>['decimal',2],
            'value' => function($model) {
                return $model->quantity * $model->price ;
            },
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummary' => true
        ],

        [
            'label' =>'Date of Transaction',
            'attribute'=>'trans_date',
            'value'=>'trans_date',
            'filterType'=>GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'pluginOptions'=>[
                    'format' => 'yyyy-mm-dd',
                   // 'autoWidget' => true,
                    'autoclose' => true,
                    'todayBtn' => true,
                ],
                'options' => ['placeholder' => 'Select Date of stock ...'],
            ],
        ],
        [
            'label' => 'Customer Name',
            'attribute' => 'customer_id',
            'value' => function($model) {
                return $model->customer->fname  . " " . $model->customer->lname ;
            },
        ],

    ];


$pdfHeader = [
    'L' => [
        'content' => ' ',
    ],
    'C' => [
        'content' => '<h3>Transactions</h3><p>Transactions Report '.$searcdate.'</p>',
        // 'font-size' => 16,
        //'font-style' => 'B',
        'font-family' => 'arial',
        'margin-bottom' => '5px',
        'color' => '#333333'
    ],
    'R' => [
        'content' => '',
    ],
    'line' => true,
];

$pdfFooter = [
    'L' => [
        'content' => '',
        'font-size' => 10,
        'color' => '#333333',
        'font-family' => 'arial',
    ],
    'C' => [
        'content' => 'Transactions',
    ],
    'R' => [
        'content' => '',
        'font-size' => 10,
        'color' => '#333333',
        'font-family' => 'arial',
    ],
    'line' => true,
];

?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
            <div class="widget-box">
<?php Pjax::begin(); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'panelHeadingTemplate' => '<div class="grid-panel-header">{title}<div class="grid-export-actions">{export}</div></div><div class="clearfix"></div>',

    'panel' => [
        'heading'=>'<i class="glyphicon glyphicon-globe"></i> Transactions',
        'type'=>'primary',
        'before'=>'',
        'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default']),
    ],
    'toolbar' => [

       '{datatoggle}',
        //['content'=>
         ////  Html::a('<i class="glyphicon glyphicon-plus"></i>',['create'],['class'=>'btn btn-success']) . ' '.
       //  Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default'])

    //],
    ],

    'pjax'=>true,
    'showHeader' => true,
        'columns' => $gridColumns,
    'exportConfig' => [
        GridView::EXCEL => [
            'label' => ( 'Excel'),
            'iconOptions' => ['class' => 'text-success'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => ('myReportExcel'),
            'alertMsg' => ( ''),
            'options' => ['title' => ( 'Microsoft Excel 95+')],
            'mime' => 'application/vnd.ms-excel',
            'config' => [
                'worksheet' => ( 'ExportWorksheet'),
                'cssFile' => ''
            ]
        ],
        GridView::PDF => [
            'filename' => 'Transactions',
            'config' => [
                'methods' => [
                    'SetHeader' => [
                        ['odd' => $pdfHeader, 'even' => $pdfHeader]
                    ],
                    'SetFooter' => [
                        ['odd' => $pdfFooter, 'even' => $pdfFooter]
                    ],
                ],
                'options' => [
                    'title' => 'Transactions Report '.$searcdate,
                    'subject' => 'Transactions',
                    'keywords' => ''
                ],
            ]
        ],
    ],
       'showPageSummary' => true,
   // 'showConfirmAlert' => false,

    ]);

    ?>
<?php Pjax::end(); ?>
    </div>
    </div>
 </div>
    </div>
