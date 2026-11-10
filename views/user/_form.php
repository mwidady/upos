<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
//use kartik\depdrop\DepDrop;
use app\models\Stations;
use  app\models\Branch;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-fluid">
    <hr>
    <div class="row-fluid">
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                    <h5>User-info</h5>
        </div>  
    <div class="widget-content">
    <?php $form = ActiveForm::begin(); ?>
        <div class="control-group">
        <div class="controls">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true,'class' => 'span11']) ?>
        </div>
        </div>
        <div class="control-group">
        <div class="controls">
        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true,'class' => 'span11']) ?>
        </div>
        </div>
        <div class="control-group">
        <div class="controls">
        <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true,'class' => 'span11']) ?>
        </div>
        </div>
        <div class="control-group">
        <div class="controls">
        <?= $form->field($model, 'username')->textInput(['maxlength' => true,'class' => 'span11']) ?>
        </div>
        </div>
        <div class="control-group">
        <div class="controls">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true,'class' => 'span11']) ?>
        </div>
        </div>

        <div class="control-group">
        <div class="controls">
        <?= $form->field($model, 'status')->dropDownList(\app\models\User::statusOptions(), ['class' => 'span11']) ?>
        </div>
        </div>

        <div class="control-group">
        <div class="controls">
        <?php
            $stations = ArrayHelper::map(Stations::find()->asArray()->all(),'id', 'name'); 
            echo $form->field($model, 'station_id')->widget(Select2::classname(), [
                'data' => $stations,
                'language' => 'en',
                'options' => ['placeholder' => 'Select a Station ...',
                'required' =>true],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
        </div>
        </div>

        <div class="control-group">
        <div class="controls">
        <?php
           // $branches = ArrayHelper::map(Branch::find()->asArray()->all(),'id', 'name'); 
            //echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
             //   'type' => DepDrop::TYPE_SELECT2,
              //  'data' => $branches,
              //  'language' => 'en',
               // 'options' => ['placeholder' => 'Select a Branch ...',
               // 'required' =>true],
              //  'pluginOptions' => [
              //      'depends'=>[Html::getInputId($model, 'station_id')],
               //     'allowClear' => true,
               //     'url'=>Url::to(['/user/branch'])
               // ],
           // ]);

 $branches = ArrayHelper::map(Branch::find()->asArray()->all(),'id', 'name'); 
            echo $form->field($model, 'branch_id')->widget(Select2::classname(), [
                'data' => $branches,
                'language' => 'en',
                'options' => ['placeholder' => 'Select a Branch ...',
                'required' =>true],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
        </div>
        </div>


        <!-- <div class="control-group">
        <div class="controls">
        <?php //echo $form->field($model, 'user_level')->dropDownList([ 'Super Admin' => 'Super Admin', 'Admin' => 'Admin', ], ['prompt' => '','class' => 'span11']) ?>
        </div>
        </div> -->
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
</div>
</div>
</div>
