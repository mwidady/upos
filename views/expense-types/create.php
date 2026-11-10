<?php

$this->title = 'Create Expense Type';
$this->params['breadcrumbs'][] = ['label' => 'Expense Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', ['model' => $model]) ?>

