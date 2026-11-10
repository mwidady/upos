<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;
use yii\widgets\Pjax;
use app\models\ExpenseType;

$this->title = 'Expense Types';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="expense-types-index">
        <p>
            <?= Html::a('Create Expense Type', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('View Expenses', ['/expenditures/index'], ['class' => 'btn btn-default']) ?>
        </p>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {
                return ['style' => $model->status == ExpenseType::STATUS_DISABLED ? 'background-color:#f5f5f5;color:#999;' : ''];
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                [
                    'attribute' => 'status',
                    'filter' => ExpenseType::statusOptions(),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->status == ExpenseType::STATUS_ENABLED
                            ? '<span class="label label-success">Enabled</span>'
                            : '<span class="label">Disabled</span>';
                    },
                ],
                'createdBy.username',
                'created_at',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Action',
                    'template' => '{update} {toggle}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], [
                                'class' => 'btn btn-info btn-xs',
                                'title' => 'Update',
                            ]);
                        },
                        'toggle' => function ($url, $model) {
                            $enabled = $model->status == ExpenseType::STATUS_ENABLED;
                            return Html::a($enabled ? 'Disable' : 'Enable', ['toggle-status', 'id' => $model->id], [
                                'class' => 'btn btn-xs ' . ($enabled ? 'btn-warning' : 'btn-success'),
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => ($enabled ? 'Disable' : 'Enable') . ' this expense type?',
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]) ?>
        <?php Pjax::end(); ?>
    </div>
</div>
