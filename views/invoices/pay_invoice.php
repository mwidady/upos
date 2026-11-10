<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Invoices */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="invoices-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?php if($payPartial) { ?> 
        <label>Payment Mode</label>
        <?= $form->field($model, 'status')->dropDownList(
        ['Full' => 'Full', 'Partial' => 'Partial'],
        ['prompt'=>'Select...', 'id' => 'status_id', 'onchange' => 'changeStatusType()']
        )
        ->label(false); 
        echo $form->field($model, 'amount_paid')->textInput( ['id' => 'amount_paid_id']);
        ?>
    <?php } ?>
    <?php
        $u= ['CASH' => 'CASH', 'BANK' => 'BANK', 'MOBILE_MONEY' => 'MOBILE MONEY'];
        echo $form->field($model, 'paid_through')->dropDownList($u,['class' => 'col-md-12']);
    ?>
    <div class="col-md-12">
    <div class="col-md-6">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <div class="col-md-6">
    <button type="button" id="close" class="btn btn-danger pull-right" data-dismiss="modal">
         close
    </button>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$script = <<<JS
    function changeStatusType() {
      if ($('#status_id').val() == "Full") {
        $('#amount_paid_id').closest('.form-group').hide();
      } else {
       $('#amount_paid_id').closest('.form-group').show();
      }
    }
JS;
$this->registerJs($script);