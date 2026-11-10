<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use app\components\AppGridView as GridView;
use yii\console\Request;
//use kartik\widgets\DatePicker;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TransactionsSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Profits and Losses';
$this->params['breadcrumbs'][] = $this->title;

if(isset(Yii::$app->request->queryParams['TransactionsSerach']['trans_date'])){
    $searcdate = Yii::$app->request->queryParams['TransactionsSerach']['trans_date'];
    $reportDesc = "Reports of ".$searcdate;
}else{
    $searcdate = "";
    $reportDesc ="";
}

//Yii::$app->request->queryParams['TransactionsSerach']['trans_date'];
    $gridColumns =[
        ['class' => 'kartik\grid\SerialColumn'],
        [
            'attribute'=>'item.name',
            'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        ],
        [
            'attribute'=>'quantity',
            'contentOptions' => ['style' => 'width:100px; white-space: normal;'],
        ],
        [
            'attribute' => 'item.bprice',
            'format'=>['decimal',2],
            'label' => 'Buying Amount',

        ],
        [
            'attribute' => 'price',
            'format'=>['decimal',2],
            'label' => 'Selling Amount',

        ],
        [
            'label' => 'Buying Total Amount',
            'format'=>['decimal',2],
            'value' => function($model) {
                return $model->quantity * $model->item->bprice ;
            },
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummary' => true
        ],
        [
            'label' => 'Selling Total Amount',
            'format'=>['decimal',2],
            'value' => function($model) {
                return $model->quantity * $model->price ;
            },
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummary' => true
        ],
        [
            'label' => 'Profits Gains',
                'format'=>['decimal',2],
            'value' => function($model) {
                return $model->quantity * $model->price - $model->quantity * $model->item->bprice;
            },
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummary' => true
        ],
        [
            'attribute' => 'trans_date',
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
            <?php  echo $this->render('_search_profit', ['model' => $searchModel]); ?>
            <div class="widget-box">
<?php Pjax::begin(); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'panelHeadingTemplate' => '<div class="grid-panel-header">{title}<div class="grid-export-actions">{export}</div></div><div class="clearfix"></div>',
    'panel' => [
        'heading'=>'<i class="glyphicon glyphicon-globe"></i> Profits',
        'type'=>'primary',
        'before'=> '<center>'.$reportDesc.'</center>',
        'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>', 
        ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default']),
    ],
    'toolbar' => [

       '{datatoggle}',
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
                'cssFile' => '',
                 'showConfirmAlert' => false,
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


    ]);

    ?>
<?php Pjax::end(); ?>
    </div>
    </div>
  </div>
</div>
