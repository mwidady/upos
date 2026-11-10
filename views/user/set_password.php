<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserPasswordForm */
/* @var $user app\models\User */

$this->title = 'Change Password: ' . $user->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = 'Change Password';
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="icon-lock"></i></span>
                    <h5>Change Password for <?= Html::encode($user->username) ?></h5>
                </div>
                <div class="widget-content">
                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'new_password')->passwordInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => true]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save Password', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
