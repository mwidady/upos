<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InvoicesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoices-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'invoiceno') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'generated_by') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'paid_time') ?>

    <?php // echo $form->field($model, 'paid_through') ?>

    <?php // echo $form->field($model, 'payment_type') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
