<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentTransactions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-transactions-form">
<div class="col-md-12"
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'amount_paid')->textInput() ?>


    <?= $form->field($model, 'paid_through')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'account_no')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'paid_date')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

</div>
