<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Stations;

/* @var $this yii\web\View */
/* @var $model app\models\Batches */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid">
<div class="batches-form col-md-6">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'start_at')->textInput() ?>

    <?= $form->field($model, 'end_at')->textInput() ?>

    <?php
           // $stations = ArrayHelper::map(Stations::find()->asArray()->all(),'id', 'name'); 
           // echo $form->field($model, 'station_id')->widget(Select2::classname(), [
              //  'data' => $stations,
           //     'language' => 'en',
            //    'options' => ['placeholder' => 'Select a Station ...',
              //  'required' =>true],
            //    'pluginOptions' => [
               //     'allowClear' => true,
             //   ],
           // ]);
        ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
