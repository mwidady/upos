<?php

use app\models\User;
use yii\helpers\Url;
use app\models\Items;
use yii\helpers\Html;
use app\models\Customers;
use app\components\AppGridView as GridView;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$branchId = User::findOne(Yii::$app->user->id)->branch_id;
$this->title = 'Sales';

$img = Url::to('@web/');
$this->registerJs(
    '$(document).ready(function(){ 
$("#customers-id").change(function(){
var e = document.getElementById("customers-id");
    var strSel =  e.options[e.selectedIndex].value;
    window.location.href="'.Yii::$app->urlManager->createUrl('category').'";
});
});',"");
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
                $data = ArrayHelper::map(Items::find()->joinWith(['quantitytotal' => function ($query) {
                    $query->where('quantity_t > 0');
                },] )->where(['items.branch_id'=>$branchId])->all(),'id', 'name');

                echo $form->field($model, 'name')->widget(Select2::classname(), [
                'model' => $model,
                'data' => $data,
                'attribute' => 'name',
                'language' => 'es',
                'options' => ['placeholder' => 'Select Item ...','onchange'=>'this.form.submit()',],
                'pluginOptions' => [

                'allowClear' => true,
                'class' => 'span12' ,'style'=>'margin-top:25px; width:100%;'
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
            </div><?php
            if(isset($_GET['show_customer'])){
            ?>
            <div class="widget-content">

                <?php $form = ActiveForm::begin(['action' =>'sell','enableAjaxValidation' => true]); ?>
                <label>Company Name</label>
                <?=
                $form->field($customer, 'id')
                    ->dropDownList(
                        ArrayHelper::map(Customers::find()->where(['branch_id'=>$branchId])->all(), 
                        'id', 'company'), ['onchange'=>'$.get( "'.Yii::$app->urlManager->createUrl(["items/cdetails?id="]).'"+$(this).val(),function(data){
                    $("#cdetails").html( data );
                    })']
                    )->label(false)
                ?>
         
                <hr>

                <div id="cdetails"></div>

                    <?= Html::a('<i class="icon icon-stop"></i>
                    <span>Cancel</span>', ['items/removeall'], ['class'=>'btn btn-info']) ?>
                        <?= Html::submitButton('<i class="icon icon-save"></i> <span>Sell</span>', ['class' => 'btn btn-success']) ?>

                    <?php ActiveForm::end(); ?>
                </center>
            </div>
            <?php
            }
            ?>
        </div>

    </div>
</div>
</div>
