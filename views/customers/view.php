<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customers */

$this->title = $model->fname ." ".$model->lname;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="customers-view col-md-6">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'fname',
            'lname',
            'gender',
            'phone',
            'company',
            'position',
            [
            'attribute' =>'clogo',
            'format' => 'raw',
          //  'value' =>  Html::a(Html::img(Yii::getAlias('@web').'/uploads/'.$model->clogo, ['alt'=>'some', 'class'=>'thing', 'height'=>'100px', 'width'=>'100px']), ['site/zoom']),
            'format' => ['raw'],
            ],

           // 'id',
            'customer_no',
            'address',
            'email:email',
        ],
    ]) ?>

</div>
</div>
