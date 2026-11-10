<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\StationBillPayment;

$this->title = $model->reference_no;
$this->params['breadcrumbs'][] = ['label' => 'Station Bill Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="station-bill-payments-view">
        <p>
            <?php if ($model->notification_status === StationBillPayment::STATUS_PENDING): ?>
                <?= Html::a('Mark as Notified', ['notify', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Mark this station bill payment as notified?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'station.name',
                'amount',
                'reference_no',
                'paid_by',
                'paid_date',
                'notes:ntext',
                'notification_status',
                'notified_at',
                'createdBy.username',
                'created_at',
            ],
        ]) ?>
    </div>
</div>
