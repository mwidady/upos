<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Invoices */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid">
<div class="invoices-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'invoiceno')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'generated_by')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'paid_time')->textInput() ?>

    <?= $form->field($model, 'paid_through')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payment_type')->textInput(['maxlength' => true]) ?>



    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
