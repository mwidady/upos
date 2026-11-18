<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\QuantitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Registered';
$this->params['breadcrumbs'][] = $this->title;

$filterParams = Yii::$app->request->get('QuantitySearch', []);
$fromDate = $filterParams['from_date'] ?? '';
$toDate = $filterParams['to_date'] ?? '';
$searcdate = trim($fromDate . ' - ' . $toDate, ' -');
$pdfHeader = [
    'L' => [
        'content' => ' ',
    ],
    'C' => [
        'content' => '<h3>Stock Registered</h3><p>Stock additions report ' . Html::encode($searcdate) . '</p>',
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
<?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['/quantity/index']]); ?>
    <div class="widget-content">
        <div class="row-fluid">
            <div class="span2"><?= $form->field($searchModel, 'from_date')->input('date') ?></div>
            <div class="span2"><?= $form->field($searchModel, 'to_date')->input('date') ?></div>
            <div class="span2"><?= $form->field($searchModel, 'item_name')->textInput(['placeholder' => 'Item name']) ?></div>
            <div class="span1"><?= $form->field($searchModel, 'quantity')->input('number', ['min' => 0, 'placeholder' => 'Qty']) ?></div>
            <div class="span2"><?= $form->field($searchModel, 'added_by')->textInput(['placeholder' => 'Username or name']) ?></div>
            <div class="span2"><?= $form->field($searchModel, 'branch_id')->dropDownList($branchOptions, ['prompt' => 'All branches']) ?></div>
            <div class="span1" style="padding-top:24px;">
                <?= Html::submitButton('<i class="icon-search"></i> Filter', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?= Html::errorSummary($searchModel, ['class' => 'alert alert-error']) ?>
        <?= Html::a('<i class="icon-repeat"></i> Reset', ['/quantity/index'], ['class' => 'btn btn-default']) ?>
    </div>
<?php ActiveForm::end(); ?>
<?php Pjax::begin(); ?>
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'panelHeadingTemplate' => '<div class="grid-panel-header">{title}<div class="grid-export-actions">{export}</div></div><div class="clearfix"></div>',
        'panel' => [
            'heading'=>'<i class="glyphicon glyphicon-globe"></i> Stock Registered',
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
            [
                'label' => 'Item Name',
                'value' => function ($model) {
                    return $model->item ? $model->item->name : '';
                },
            ],
            'quantity',
            [
                'label' => 'Added By',
                'value' => function ($model) {
                    if (!$model->user) {
                        return '';
                    }
                    $name = trim($model->user->first_name . ' ' . $model->user->last_name);
                    return $name ?: $model->user->username;
                },
            ],
            [
                'label' => 'Branch',
                'value' => function ($model) {
                    return $model->item && $model->item->branch ? $model->item->branch->name : '';
                },
            ],
            [
                'attribute' => 'q_date',
                'label' => 'Date Added',
                'format' => 'datetime',
            ],
        ],
    'exportConfig' => [
        GridView::EXCEL => [
            'label' => ( 'Excel'),
            'iconOptions' => ['class' => 'text-success'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => 'stock-registered',
            'alertMsg' => ( ''),
            'options' => ['title' => ( 'Microsoft Excel 95+')],
            'mime' => 'application/vnd.ms-excel',
            'config' => [
                'worksheet' => ( 'ExportWorksheet'),
                'cssFile' => ''
            ]
        ],
        GridView::PDF => [
            'filename' => 'stock-registered',
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
                    'title' => 'Stock Registered Report ' . $searcdate,
                    'subject' => 'Stock Registered',
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
