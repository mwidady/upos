<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Workflow $model */

$this->title = $model->workflow_code;
$this->params['breadcrumbs'][] = ['label' => 'Workflows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="workflow-view">
<hr>
    <div class="row-fluid">
        <div class="span3">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
            'label' => 'Brach Name',
            'value'=>'branch.name',
            ],
            'workflow_code',
        ],
    ]) ?>

</div></div></div>
