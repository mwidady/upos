<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Audit Entry #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Audit Trail', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$jsonBlock = function ($value) {
    if (!$value) { return '<span class="text-muted">No values recorded</span>'; }
    $decoded = json_decode($value, true);
    $display = $decoded === null ? $value : json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return Html::tag('pre', Html::encode($display), ['style' => 'margin:0; white-space:pre-wrap']);
};
?>
<div class="audit-trail-view">
    <p><?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Back to Audit Trail', ['index'], ['class' => 'btn btn-default']) ?></p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            ['attribute' => 'user_id', 'value' => $model->user ? $model->user->username : 'System'],
            ['attribute' => 'branch_id', 'value' => $model->branch ? $model->branch->name : '-'],
            'action', 'model', 'record_id', 'description', 'route', 'ip_address', 'user_agent',
            ['attribute' => 'old_values', 'format' => 'raw', 'value' => $jsonBlock($model->old_values)],
            ['attribute' => 'new_values', 'format' => 'raw', 'value' => $jsonBlock($model->new_values)],
            ['attribute' => 'created_at', 'format' => ['datetime', 'php:d M Y, H:i:s']],
        ],
    ]) ?>
</div>
