<?php


/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */


// use yii\bootstrap5\Html;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

Html::beginForm('/logout');
Html::a('Logout', '#', ['data-confirm' => 'Are you sure?']);
Html::endForm();
?>

<section>
      <div class="container">
        <div class="row">
          <div class="col-md-6 col-md-push-3">
            <h4 class="text-gray mt-0 pt-5"> LOGIN </h4>
            <hr>
            <p>Fill the data required</p>
         
            <?php $form = ActiveForm::begin([
        'id' => 'login-form',
         'layout' => 'horizontal',
        'class'=>'clearfix',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?> 
                <div class="col-md-12">
                  <label for="form_username_email" class="row">
                    Username/Email
                </label>
                  <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label(false) ?>
                </div>

                <div class="col-md-12">
                  <label for="form_password" class="row">
                    Password
                </label>
                  <?= $form->field($model, 'password')->passwordInput()->label(false) ?>
                </div>

              <div class="checkbox pull-left mt-15">
                <label for="form_checkbox">
                  <!-- Remember me  -->
                  <?= $form->field($model, 'rememberMe')
                  ->checkbox(['template' => "<div class=\"offset-lg-1 col-lg-10 custom-control custom-checkbox\">{input} {label}</div>
                  \n<div class=\"col-lg-8\">{error}</div>"]) 
                  ?>
                </label>
              </div>

              <div class="form-group pull-right mt-10">
                <?= Html::submitButton('Login', 
                [
                    'class' => 'btn btn-dark btn-sm',
                    'name' => 'login-button',
                   'style' => 'margin-right:10px;'
                ]) ?>
              </div>

              <div class="clear text-center pt-10">
                <a class="text-theme-colored font-weight-600 font-12" href="#">Forgot Your Password?</a>
              </div>
              <?php ActiveForm::end(); ?>
          </div>
        </div>
      </div>
    </section>