<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Items */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid">
    <hr>
    <div class="row-fluid">
        <?php $form = ActiveForm::begin(); ?>
    <div class="span5">
        <div class="widget-box">
            <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                <h5>Item-info</h5>
            </div>
            <div class="widget-content">


    <div class="control-group">
          <div class="controls">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true,'class' => 'span11']) ?>
          </div>
    </div>
        <div class="control-group">
        <div class="controls">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true,'class' => 'span11']) ?>
        </div>
        </div>
        <div class="control-group">
        <div class="controls">
            <?= $form->field($model, 'brand')->textInput(['maxlength' => true,'class' => 'span11']) ?>
        </div>
        </div>
        <div class="control-group">
        <div class="controls">
            <?= $form->field($model, 'bprice')->textInput(['class' => 'span11']) ?>
        </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <?= $form->field($model, 'sprice')->textInput(['class' => 'span11']) ?>
            </div>
        </div>


</div>
</div>
</div>


        <div class="span6" style="">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                    <h5>Item-info</h5>
                </div>
                <div class="widget-content">

                    <div class="control-group">
                        <div class="controls">
                            <?php
                            $u= ['normal' => 'Normal', 'kg' => 'Kilogram', 'Litre' => 'Litre'];
                            echo $form->field($model, 'unit')->dropDownList($u,['class' => 'span11']);
                            ?>
                        </div>
                    </div>


                    <div class="form-actions">
                        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>


                </div>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>


