<?php

use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\Html;
use app\components\AppGridView as GridView;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = 'Sales';
$customerDetailsUrl = Json::htmlEncode(Url::to(['/items/cdetails']));
$selectedCustomerId = Json::htmlEncode((string) $customer->id);
$this->registerJs("jQuery(function($) {
    var customerDetailsUrl = {$customerDetailsUrl};
    $('#customers-id').on('change', function() {
        var customerId = $(this).val();
        if (!customerId) {
            $('#cdetails').empty();
            return;
        }
        $.get(customerDetailsUrl, {id: customerId}, function(data) {
            $('#cdetails').html(data);
        });
    });
    if ({$selectedCustomerId}) {
        $.get(customerDetailsUrl, {id: {$selectedCustomerId}}, function(data) {
            $('#cdetails').html(data);
        });
    }
});");

?>

<div class="container-fluid">
<div class="row-fluid">
    <div class="span8">
        <div class="widget-box">
            <div class="widget-title"> <span class="icon icon-list">
            </span>
                <h5>Sales</h5>
            </div>
            <div class="widget-content">
                <center>
                    <?php $form = ActiveForm::begin(
                        ['method'=>'POST', 'action' => ['sales'],'enableClientValidation' => false,
                        'enableAjaxValidation' => false]); ?>
                   <table style="width:60%">
                       <tr>
                  
                           <td style="padding-top:14px;">
                <?php
                echo $form->field($model, 'name')->widget(Select2::classname(), [
                'model' => $model,
                'data' => [],
                'attribute' => 'name',
                'options' => [
                    'placeholder' => 'Select Item ...',
                    'onchange' => 'this.form.submit()',
                    'class' => 'span12',
                    'style' => 'margin-top:25px; width:100%;',
                ],
                'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::to(['/items/search-sale-items']),
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params) { return {q: params.term, page: params.page || 1}; }'),
                    'processResults' => new JsExpression('function(data) { return {results: data.results, pagination: {more: data.more}}; }'),
                ],
                'width' => '100%',
                ],
                ])->label(false);?>
           </td>
             <td><?php //e Html::submitButton('Submit', ['class' =>'btn btn-primary']) ?></td>
                       </tr>

                   </table>

                    <?php ActiveForm::end(); ?>
                    <?php
                    if(Yii::$app->session->hasFlash('error')){
                     ?>
                    <div class="alert alert-danger">
                        <?php
                           echo Yii::$app->session->getFlash('error');
                        ?>
                    </div>
                    <?php
                    }
                    ?>
                </center>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'showPageSummary' => true,
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],
        'item.name',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'price',
            'value' => 'price',

        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'quantity',
            'value' => 'quantity',

        ],
        [
            'label' =>'+/-',
            'format' =>'html',
            'value' => function($model){
              //  $rem = '';
                if($model->quantity > 1) {
                    $rem = Html::a('<span class="glyphicon glyphicon-minus"></span>',
                        ['items/removequantity?id='.$model->id],
                        ['class' => 'btn btn-danger btn-number btn-mini']);
                }else{
                    $rem = Html::a('<span class="glyphicon glyphicon-minus"></span>',
                        ['#'], ['class' => 'btn btn-danger btn-number btn-mini']);
                }
                $add = Html::a('<span class="glyphicon glyphicon-plus"></span>',
                ['items/addquantity?id='.$model->id], 
                ['class'=>'btn btn-success btn-number btn-mini']);
                return $rem." ".$add;
            },
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
            'label'=>'Remove',
            'attribute' =>'id',
            'format'=>'html',
            'value' => function($model){
                return Html::a('<i class="icon icon-remove"></i> <span>Remove</span>', ['items/remove?id='.$model->id], ['class'=>'btn btn-primary btn-mini']);
            },

            'pageSummary' =>Html::a('<i class="icon icon-remove"></i> <span>Remove All</span>', ['items/removeall'], ['class'=>'btn btn-danger btn-mini']),
        ],
    ]
]); ?>
</div>
        </div>
    </div>
    <div class="span4">
        <div class="widget-box">
            <div class="widget-title"> <span class="icon icon-list">
            </span>
                <h5>Customer / Company</h5>
            </div>
            <div class="widget-content">

                <?php $form = ActiveForm::begin(['action' => ['/items/sell'], 'enableAjaxValidation' => false]); ?>
                <label>Company Name</label>
                <?=
                $form->field($customer, 'id')->widget(Select2::classname(), [
                    'data' => $customerOptions,
                    'options' => [
                        'placeholder' => 'Search customer or company ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => Url::to(['/items/search-sale-customers']),
                            'dataType' => 'json',
                            'delay' => 250,
                            'data' => new JsExpression('function(params) { return {q: params.term, page: params.page || 1}; }'),
                            'processResults' => new JsExpression('function(data) { return {results: data.results, pagination: {more: data.more}}; }'),
                        ],
                    ],
                ])->label(false)
                ?>
         
                <hr>

                <div id="cdetails"></div>

                    <?= Html::a('<i class="icon icon-stop"></i>
                    <span>Cancel</span>', ['items/removeall'], ['class'=>'btn btn-info']) ?>
                        <?= Html::submitButton('<i class="icon icon-save"></i> <span>Sell</span>', ['class' => 'btn btn-success']) ?>

                    <?php ActiveForm::end(); ?>
            </div>
        </div>

    </div>
</div>
</div>
