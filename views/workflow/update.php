<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Workflow $model */

$this->title = 'Update Workflow: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Workflows', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="workflow-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
