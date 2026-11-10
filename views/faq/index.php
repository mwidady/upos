<?php

use yii\helpers\Html;
use app\components\AppGridView as GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FaqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Faqs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="faq-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Faq', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'question:ntext',
            'answer:ntext',
            'createdBy.username',
            //'created_at',

            ['class' => 'yii\grid\ActionColumn', 'header' => 'Action'],
        ],
    ]); ?>
</div>
</div>
