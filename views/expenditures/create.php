<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Expenditures */

$this->title = 'Create Expense';
$this->params['breadcrumbs'][] = ['label' => 'Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expenditures-create">
    <?= $this->render('_form', [
        'model' => $model,
        'expenseTypes' => $expenseTypes,
    ]) ?>

</div>

