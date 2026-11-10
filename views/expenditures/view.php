<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Expenditures */

$this->title = $model->ename;
$this->params['breadcrumbs'][] = ['label' => 'Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="expenditures-view col-md-6">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          //  'id',
            'ename',
            [
                'label' => 'Expense Type',
                'value' => $model->expenseType ? $model->expenseType->name : $model->etype,
            ],
            'edate',
            [
                 'attribute' => 'amount',
                'format'=>['decimal',2],
            ],
            'amount',
            'company',
            'phone',
            //'user_id',
        ],
    ]) ?>

</div>
</div>

