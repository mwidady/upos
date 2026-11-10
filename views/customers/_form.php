<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Customers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-fluid">
    <hr>
    <div class="row-fluid">
        <?php $form = ActiveForm::begin(['options' => ['enctype'=>'multipart/form-data']]); ?>
        <div class="span5">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                    <h5>Customer-info</h5>
                </div>
                <div class="widget-content">

    <?= $form->field($model, 'fname')->textInput(['maxlength' => true,'class' => 'span11']) ?>

    <?= $form->field($model, 'lname')->textInput(['maxlength' => true,'class' => 'span11']) ?>


    <?= $form->field($model, 'phone')->textInput(['maxlength' => true,'class' => 'span11']) ?>

    <?= $form->field($model, 'company')->textInput(['maxlength' => true,'class' => 'span11']) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true,'class' => 'span11']) ?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true,'class' => 'span11']) ?>

</div>
</div>
</div>


        <div class="span6">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                    <h5>Customer-info</h5>
                </div>
                <div class="widget-content">


                    <?php
                    if ($model->isNewRecord){
                   echo  $form->field($model, 'clogo')->widget(FileInput::classname(), [
                        'options' => ['accept' => 'image/*'],'class' => 'span2',
                    ]);
                   }
                    ?>

                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', 
                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>


                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
</div>
</div>
