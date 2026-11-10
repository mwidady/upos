<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionsSerach */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transactions-search">

    <center>
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]);

 $addon = <<< HTML
    <span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
    </span>
HTML;


                echo DateRangePicker::widget([
                        'model'=>$model,
                        'attribute' => 'trans_date',
                        'useWithAddon'=>false,
                        'convertFormat'=>true,

                    'options' => ['class' => 'form-control','style' =>'width:400px; paddinf:2px;'],
                        'pluginOptions'=>[
                            'locale'=>['format' => 'Y-m-d',
                                'separator'=>' - '
                            ],
                        ],
                    ]);

                ?>

        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </center>

</div>
