<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Quantity */

$this->title = 'Create Quantity';
$this->params['breadcrumbs'][] = ['label' => 'Quantities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quantity-create">
    <?= $this->render('_form', [
        'model' => $model,
        'provider_quantity' => $provider_quantity,
        'provider' => $provider,
        'allItems'=>$allItems
    ]) ?>

</div>
