<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Expenditures */
/* @var $expenseTypes array */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid">
    <hr>
    <div class="row-fluid">
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                    <h5>Expense Details</h5>
                </div>
                <div class="widget-content">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ename')->textInput(['maxlength' => true,'class' => 'span11']) ?>

    <?= $form->field($model, 'expense_type_id')->dropDownList($expenseTypes, [
        'prompt' => 'Select Expense Type',
        'class' => 'span11',
    ]) ?>
    <p><?= Html::a('Manage expense types', ['/expense-types/index']) ?></p>
    <?= $form->field($model, 'amount')->textInput(['class' => 'span11']) ?>

    <?= $form->field($model, 'company')->textInput(['maxlength' => true,'class' => 'span11']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
</div>
</div>
</div>

