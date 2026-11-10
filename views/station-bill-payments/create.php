<?php

$this->title = 'Create Station Bill Payment';
$this->params['breadcrumbs'][] = ['label' => 'Station Bill Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
