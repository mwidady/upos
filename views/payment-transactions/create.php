<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentTransactions */

$this->title = 'Create Payment Transactions';
$this->params['breadcrumbs'][] = ['label' => 'Payment Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-transactions-create">
<div class="col-md-12">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</div>
