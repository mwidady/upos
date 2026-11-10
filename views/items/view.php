<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Items */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="items-view col-md-6">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'name',
            'description',
           [
            'attribute' =>'bprice',
                'format'=>['decimal',2],
           ] ,
            [
            'attribute' =>'sprice',
                'format'=>['decimal',2],
           ],
          //  'vat',
            //'user.username',
            'unit',
           // 'supplier',
           // 'sphone',
        ],
    ]) ?>

</div>
</div>
