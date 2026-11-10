<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\WorkflowConstant;
use  app\models\Branch;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/** @var yii\web\View $this */
/** @var app\models\Workflow $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="workflow-form">
<div class="container-fluid">
    <hr>
    <div class="row-fluid">
        <div class="span3">

    <?php $form = ActiveForm::begin(); ?>
    <?php
    $branches = ArrayHelper::map(Branch::find()->asArray()->all(),'id', 'name'); 
    echo $form->field($model, 'branch_id')->widget(Select2::classname(), [
        'data' => $branches,
        'language' => 'en',
        'options' => ['placeholder' => 'Select a Branch ...',
        'required' =>true],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Branch');
    ?>
    <?php
        echo $form->field($model, 'workflow_code')
        ->dropDownList(WorkflowConstant::$workflowTypes,['class' => 'span12 control-group'])
        ->label('Code');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
</div>
</div>
