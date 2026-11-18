<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\ChangePasswordForm */
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

            <?= $form->field($model, 'current_password')->passwordInput([
                'maxlength' => true,
                'autocomplete' => 'current-password',
            ]) ?>

            <?= $form->field($model, 'new_password')->passwordInput([
                'maxlength' => true,
                'autocomplete' => 'new-password',
            ]) ?>

            <?= $form->field($model, 'repeat_password')->passwordInput([
                'maxlength' => true,
                'autocomplete' => 'new-password',
            ]) ?>

            <?php if ($message = Yii::$app->session->getFlash('success')): ?>
                <div class="alert alert-success" role="alert"><?= Html::encode($message) ?></div>
            <?php endif; ?>

            <div class="form-group">
                <?= Html::submitButton('Change', ['class' =>'btn btn-primary']) ?>
            </div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
</div>
</div>
