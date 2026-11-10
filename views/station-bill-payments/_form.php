<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Stations;

$stations = ArrayHelper::map(Stations::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="icon-align-justify"></i></span>
                    <h5>Station Bill Payment</h5>
                </div>
                <div class="widget-content">
                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'station_id')->dropDownList($stations, ['prompt' => 'Select station']) ?>
                    <?= $form->field($model, 'amount')->textInput() ?>
                    <?= $form->field($model, 'reference_no')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'paid_by')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'paid_date')->input('date') ?>
                    <?= $form->field($model, 'notes')->textarea(['rows' => 4]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
