<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\ExpenseType;

/* @var $model app\models\ExpenseType */
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title"><span class="icon"><i class="icon-align-justify"></i></span><h5>Expense Type Details</h5></div>
                <div class="widget-content">
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'span11']) ?>
                    <?= $form->field($model, 'status')->dropDownList(ExpenseType::statusOptions(), ['class' => 'span11']) ?>
                    <?= Html::submitButton($model->isNewRecord ? 'Create Expense Type' : 'Save Changes', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

