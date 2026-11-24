<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Sign in | LYIORA POS';
?>
<main class="login-page">
    <div class="login-card">
        <section class="login-brand" aria-label="LYIORA Point of Sale">
            <div class="login-brand-glow" aria-hidden="true"></div>
            <?= Html::img(Url::to('@web/images/lyiora-lighting.webp'), [
                'class' => 'login-brand-logo',
                'alt' => 'LYIORA',
            ]) ?>
            <div class="login-brand-copy">
                <span class="login-brand-kicker">LYIORA / POINT OF SALE</span>
                <h1>Every sale, clearly in view.</h1>
                <p>Manage items, invoices, and branch activity from one place.</p>
            </div>
            <span class="login-brand-caption">A brighter way to do business.</span>
        </section>

        <section class="login-panel" aria-labelledby="login-heading">
            <div class="login-heading">
                <span class="login-eyebrow">SECURE ACCESS</span>
                <h2 id="login-heading">Welcome back</h2>
                <p>Sign in to continue to your workspace.</p>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['class' => 'login-form'],
            ]); ?>
                <?= $form->field($model, 'username', [
                    'options' => ['class' => 'login-field'],
                ])->textInput([
                    'autofocus' => true,
                    'autocomplete' => 'username',
                    'placeholder' => 'Enter your username',
                ])->label('Username') ?>

                <?= $form->field($model, 'password', [
                    'options' => ['class' => 'login-field'],
                ])->passwordInput([
                    'autocomplete' => 'current-password',
                    'placeholder' => 'Enter your password',
                ])->label('Password') ?>

                <div class="login-form-options">
                    <?= $form->field($model, 'rememberMe', [
                        'options' => ['class' => 'login-remember-field'],
                    ])->checkbox(['class' => 'login-remember-input'])->label('Remember me') ?>
                </div>

                <?= Html::submitButton('Sign in', [
                    'class' => 'btn login-submit',
                    'name' => 'login-button',
                ]) ?>
            <?php ActiveForm::end(); ?>

            <p class="login-security-note">Access is managed by your station administrator.</p>
        </section>
    </div>

    <footer class="login-page-footer">LYIORA POINT OF SALE</footer>
</main>
