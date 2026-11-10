<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Batches */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Batches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container-fluid">
<div class="batches-view">

    <h1>Created #: <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
  
  		//echo Html::a('Delete', ['delete', 'id' => $model->id], [
           // 'class' => 'btn btn-danger',
          //  'data' => [
             //   'confirm' => 'Are you sure you want to delete this item?',
             //   'method' => 'post',
           // ],
       // ]) 
      ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            'start_at',
            'end_at',
            'generatedBy.username',
            'station.name',
            'created_at',
        ],
    ]) ?>

</div>
</div>
