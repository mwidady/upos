<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Workflow $model */

$this->title = 'Create Workflow';
$this->params['breadcrumbs'][] = ['label' => 'Workflows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workflow-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
