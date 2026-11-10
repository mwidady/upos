<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Alert;
/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-fluid">
    <hr>
    <div class="row-fluid">
<div class="col-sm-5">
    <?php $form = ActiveForm::begin(); ?>
    <div class="widget-box">
    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
        <h5>Change Password</h5>
    </div>
    <div class="widget-content">

            <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'new_password')->textInput(['maxlength' => true,]) ?>

            <?= $form->field($model, 'repeat_password')->textInput(['maxlength' => true,'class' =>'form-control']) ?>

            <div class="form-group">
                <?= Html::submitButton('Change', ['class' =>'btn btn-primary']) ?>
            </div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
    if($flash = Yii::$app->session->getFlash('error')){
       // echo Alert::widget(['options' => ['class' => 'alert-danger','delay' => 1000], 'body' => $flash,]);
        ?>
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Error!</strong> Invalid Current Password!
        </div>
    <?php
    } ?>

</div>
</div>
</div>
