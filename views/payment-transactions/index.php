<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentTransactionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

setlocale(LC_MONETARY, 'nl_NL.UTF-8');
$this->title = 'Payment Transactions';
$this->params['breadcrumbs'][] = $this->title;

if(isset(Yii::$app->request->queryParams['PaymentTransactionsSearch']['paid_date'])){
    $searcdate = Yii::$app->request->queryParams['PaymentTransactionsSearch']['paid_date'];
}else{
    $searcdate = "";
}
$gridColumns =[
    ['class' => 'kartik\grid\SerialColumn'],
    'paid_date',
    'paid_through',
    'receivedBy.username',
    [
        'label' => 'initial_amount',
        'format'=>['decimal',2],
        'value' => function($model) {
            return $model->initial_amount;
        },
        'pageSummaryFunc' => GridView::F_SUM,
        'pageSummary' => true
    ],
    [
        'label' => 'amount_remain',
        'format'=>['decimal',2],
        'value' => function($model) {
            return $model->amount_remain;
        },
        'pageSummaryFunc' => GridView::F_SUM,
        'pageSummary' => true
    ],
    [
        'label' => 'amount_paid',
        'format'=>['decimal',2],
        'value' => function($model) {
            return $model->amount_paid;
        },
        'pageSummaryFunc' => GridView::F_SUM,
        'pageSummary' => true
    ]
];


$pdfHeader = [
    'L' => [
        'content' => ' ',
    ],
    'C' => [
        'content' => '<h3>Payment Reports</h3><p>Payments Report '.$searcdate.'</p>',
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
        <p>
    </p><div class="widget-box">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panelHeadingTemplate' => '<div class="grid-panel-header">{title}<div class="grid-export-actions">{export}</div></div><div class="clearfix"></div>',

                'panel' => [
                    'heading'=>'<i class="glyphicon glyphicon-globe"></i> Payments',
                    'type'=>'primary',
                    'before'=>'',
                    'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default']),
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
                            'cssFile' => ''
                        ]
                    ],
                    GridView::PDF => [
                        'filename' => 'Payments',
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
                                'title' => 'Payments Report '.$searcdate,
                                'subject' => 'Payments',
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
