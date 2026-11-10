<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Stations */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Stations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container-fluid">
<div class="stations-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
            ((int) $model->status === \app\models\Stations::STATUS_ENABLED ? 'Disable Station' : 'Enable Station'),
            ['togglestatus', 'id' => $model->id],
            [
                'class' => ((int) $model->status === \app\models\Stations::STATUS_ENABLED ? 'btn btn-danger' : 'btn btn-success'),
                'data' => [
                    'confirm' => ((int) $model->status === \app\models\Stations::STATUS_ENABLED ? 'Disable this station and restrict all its users from login?' : 'Enable this station and allow its users to login again?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
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
            //'id',
            'name',
            'description:ntext',
            'address',
            'location',
            'tel',
            'fax',
            'email:email',
            [
                'attribute' => 'status',
                'value' => ((int) $model->status === \app\models\Stations::STATUS_ENABLED ? 'Enabled' : 'Disabled'),
            ],
            'created_at',
            [
                'attribute' =>'slogo',
                'format' => 'raw',
                'value' =>  Html::a(Html::img(Yii::getAlias('@web').'/uploads/stations/'.$model->slogo, ['alt'=>'some', 'class'=>'thing', 'height'=>'100px', 'width'=>'100px']), ['site/zoom']),
                'format' => ['raw'],
                ],
        ],
    ]) ?>

</div>
</div>
