<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ExpendituresSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
setlocale(LC_MONETARY, 'nl_NL.UTF-8');
$this->title = 'Expenses';
$this->params['breadcrumbs'][] = $this->title;


if(isset(Yii::$app->request->queryParams['ExpendituresSearch']['edate'])){
    $searcdate = Yii::$app->request->queryParams['ExpendituresSearch']['edate'];
}else{
    $searcdate = "";
}
$gridColumns =[
    ['class' => 'kartik\grid\SerialColumn'],
    'ename',
    [
        'attribute' => 'etype',
        'label' => 'Expense Type',
        'value' => function ($model) {
            return $model->expenseType ? $model->expenseType->name : $model->etype;
        },
    ],
    'edate',
    [
        'label' => 'Amount',
        'format'=>['decimal',2],
        'value' => function($model) {
            return $model->amount;
        },
        'pageSummaryFunc' => GridView::F_SUM,
        'pageSummary' => true
    ],
    'company',
    'phone',
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
];


$pdfHeader = [
    'L' => [
        'content' => ' ',
    ],
    'C' => [
        'content' => '<h3>Expenses</h3><p>Expenses Report '.$searcdate.'</p>',
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
        <?= Html::a('Create Expense', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Expense Types', ['/expense-types/index'], ['class' => 'btn btn-default']) ?>
    </p><div class="widget-box">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panelHeadingTemplate' => '<div class="grid-panel-header">{title}<div class="grid-export-actions">{export}</div></div><div class="clearfix"></div>',

                'panel' => [
                    'heading'=>'<i class="glyphicon glyphicon-globe"></i> Expenses',
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
                        'filename' => 'Expenses',
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
                                'title' => 'Expenses Report '.$searcdate,
                                'subject' => 'Expenses',
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
