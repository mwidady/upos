<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Expenditures */

$this->title = 'Update Expense: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="expenditures-update">
    <?= $this->render('_form', [
        'model' => $model,
        'expenseTypes' => $expenseTypes,
    ]) ?>

</div>

