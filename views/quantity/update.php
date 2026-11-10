<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quantity */

$this->title = 'Update Quantity: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quantities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quantity-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
