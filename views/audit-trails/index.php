<?php

use app\models\AuditTrail;
use app\components\AppGridView as GridView;
use yii\helpers\Html;

$this->title = 'Audit Trail';
$this->params['breadcrumbs'][] = ['label' => 'Settings'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-trail-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'hover' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<i class="glyphicon glyphicon-time"></i> Audit Trail',
        ],
        'toolbar' => ['{toggleData}'],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d M Y, H:i:s'],
                'filterInputOptions' => ['class' => 'form-control', 'type' => 'date'],
                'width' => '175px',
            ],
            [
                'attribute' => 'username',
                'value' => function ($model) { return $model->user ? $model->user->username : 'System'; },
            ],
            [
                'attribute' => 'action',
                'filter' => AuditTrail::actionOptions(),
                'format' => 'raw',
                'value' => function ($model) {
                    $classes = [
                        AuditTrail::ACTION_CREATE => 'success', AuditTrail::ACTION_UPDATE => 'info',
                        AuditTrail::ACTION_DELETE => 'danger', AuditTrail::ACTION_LOGIN => 'primary',
                        AuditTrail::ACTION_LOGOUT => 'default',
                    ];
                    $label = AuditTrail::actionOptions()[$model->action] ?? $model->action;
                    return Html::tag('span', Html::encode($label), ['class' => 'label label-' . ($classes[$model->action] ?? 'default')]);
                },
            ],
            'model', 'record_id', 'description', 'route', 'ip_address',
            ['class' => 'kartik\grid\ActionColumn', 'header' => 'Action', 'template' => '{view}', 'width' => '70px'],
        ],
    ]) ?>
</div>
