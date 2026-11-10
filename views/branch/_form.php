<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Stations;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Branch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid">
<div class="branch-form col-md-6">

    <?php $form = ActiveForm::begin(); ?>

    <div class="control-group">
        <div class="controls">
        <?php
            $stations = ArrayHelper::map(Stations::find()->asArray()->all(),'id', 'name'); 
            echo $form->field($model, 'station_id')->widget(Select2::classname(), [
                'data' => $stations,
                'language' => 'en',
                'options' => ['placeholder' => 'Select a Station ...',
                'required' =>true],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
        </div>
        </div>

<div class="control-group">
<div class="controls">
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="control-group">
        <div class="controls">
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    </div>
</div>
    <div class="control-group">
        <div class="controls">
    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
    </div>
</div>
    <div class="control-group">
        <div class="controls">
    <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>
    </div>
</div>
    <div class="control-group">
        <div class="controls">
    <?= $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>
    </div>
</div>
    <div class="control-group">
        <div class="controls">
    <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>
    </div>
</div>
    <div class="control-group">
        <div class="controls">
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
</div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
