<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid col-md-6">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Change Password', ['setpassword', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a(
            ((int) $model->status === \app\models\User::STATUS_ENABLED ? 'Disable User' : 'Enable User'),
            ['togglestatus', 'id' => $model->id],
            [
                'class' => ((int) $model->status === \app\models\User::STATUS_ENABLED ? 'btn btn-danger' : 'btn btn-success'),
                'data' => [
                    'confirm' => ((int) $model->status === \app\models\User::STATUS_ENABLED ? 'Disable this user account?' : 'Enable this user account?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
        //    'id',
            'first_name',
            'last_name',
            'phone_number',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'value' => ((int) $model->status === \app\models\User::STATUS_ENABLED ? 'Enabled' : 'Disabled'),
            ],
         //   'authKey',
         //   'password_reset_token',
         //   'user_image',
            'branch.name',
            'branch.station.name',
        ],
    ]) ?>

</div>
