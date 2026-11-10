<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
use app\models\StationBillPayment;

$this->title = 'Station Bill Payments';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="station-bill-payments-index">
        <p>
            <?= Html::a('Create Station Bill Payment', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {
                return [
                    'style' => $model->notification_status === StationBillPayment::STATUS_PENDING ? 'background-color:#fcf8e3;' : '',
                ];
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'station.name',
                'amount',
                'reference_no',
                'paid_by',
                'paid_date',
                [
                    'attribute' => 'notification_status',
                    'filter' => StationBillPayment::notificationOptions(),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->notification_status === StationBillPayment::STATUS_PENDING
                            ? '<span class="label label-warning">Pending</span>'
                            : '<span class="label label-success">Notified</span>';
                    }
                ],
                'createdBy.username',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Action',
                    'template' => '{view} {notify}',
                    'buttons' => [
                        'notify' => function ($url, $model) {
                            if ($model->notification_status === StationBillPayment::STATUS_NOTIFIED) {
                                return '';
                            }

                            return Html::a('Notify', ['notify', 'id' => $model->id], [
                                'class' => 'btn btn-warning btn-xs',
                                'data' => [
                                    'confirm' => 'Mark this station bill payment as notified?',
                                    'method' => 'post',
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
