<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\QuantitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quantities';
$this->params['breadcrumbs'][] = $this->title;

if(isset(Yii::$app->request->queryParams['QuantitySearch']['q_date'])){
    $searcdate = Yii::$app->request->queryParams['QuantitySearch']['q_date'];
}else{
    $searcdate = "";
}
$pdfHeader = [
    'L' => [
        'content' => ' ',
    ],
    'C' => [
        'content' => '<h3>Stocking</h3><p>Stocking/Purchasing Report '.$searcdate.'</p>',
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

    <div class="widget-box">
<?php Pjax::begin(); ?>
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panelHeadingTemplate' => '<div class="grid-panel-header">{title}<div class="grid-export-actions">{export}</div></div><div class="clearfix"></div>',
        'panel' => [
            'heading'=>'<i class="glyphicon glyphicon-globe"></i> Stocking',
            'type'=>'primary',
            'before'=>'',
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default']),
        ],
       'toolbar' => [
           '{datatoggle}',
         //   ['content'=>
          //   Html::a('<i class="glyphicon glyphicon-plus"></i>',['create'],['class'=>'btn btn-success']) . ' '.
           //  Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default'])

          //  ],
        ],

        'pjax'=>true,
        'showHeader' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
           //'id',
            'item.name',
            'user.username',

            [
                'label' =>'Date of Stocking',
                'attribute'=>'q_date',
                'value'=>'q_date',
                'filterType'=>GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'pluginOptions'=>[
                        'format' => 'yyyy-mm-dd',
                        'autoWidget' => true,
                        'autoclose' => true,
                        'todayBtn' => true,
                    ],
                    'options' => ['placeholder' => 'Select Date of stock ...'],
                ],
            ],
            'quantity',

          //  ['class' => 'yii\grid\ActionColumn'],
        ],
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
    ]); ?>
<?php Pjax::end(); ?>
</div>
</div>
</div>
</div>
