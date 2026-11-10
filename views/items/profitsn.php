<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

$this->title = 'Net Profits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="span12">

    <center>
        <?php $form = ActiveForm::begin([
            'action' => ['profitsn'],
            'method' => 'post',
        ]);

        $addon = <<< HTML
    <span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
    </span>
HTML;


        echo DateRangePicker::widget([
                'model'=>$model,
                'attribute' => 'ts_time',
                'useWithAddon'=>false,
                'convertFormat'=>true,
                'options' => ['class' => 'form-control','style' =>'width:400px; margin-top:10px;'],
                'pluginOptions'=>[
                    'locale'=>['format' => 'Y-m-d',
                        'separator'=>' To '
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

<div class="container-fluid">
    <div class="row-fluid">

        <div class="span12">
            <center>
                <?php
                if($pdate == 0){
                    echo "TRANSACTIONS OF TODAY ".date('Y-m-d');
                }else{
                    $sdate = explode(' To ',$pdate);
                    echo "TRANSACTIONS REPORTS FROM ".$sdate[0]." TO ".$sdate[1];
                } ?>
            </center>

        </div>
            <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
                    <h5>Transaction Profits</h5>
                </div>
                <div class="widget-content">
                    <table class="table table-bordered data-table">
                        <thead>
                        <tr>
                            <th><i class="icon-resize-vertical"></i>SNO</th>
                            <th>Date-Time</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Buying price</th>
                            <th>Selling Price</th>
                            <th>Profits</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sum = 0;
                        $count =1;
                        foreach($provider_trans AS $items) {
                            ?>
                            <tr class="gradeX">
                                <td><center><?=$count;?></center></td>
                                <td><?=$items['ts_time']; ?></td>
                                <td><?=$items['name']; ?></td>
                                <td><?=$items['quantity']; ?></td>
                                <td><?=number_format($items['bprice'],2); ?></td>
                                <td><?=number_format($items['price'],2); ?></td>
                                <td><?=number_format(($items['price'] * $items['quantity']) - ($items['bprice'] * $items['quantity']),2); ?></td>

                            </tr>
                            <?php
                            $sum += ($items['price'] * $items['quantity']) - ($items['bprice'] * $items['quantity']);
                            $count++;
                        }
                        ?>
                        </tbody>
                        <fhead>
                            <tr>
                                <th><i class="icon-resize-vertical"></i>SNO</th>
                                <th>Date-Time</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Buying price</th>
                                <th>Selling Price</th>
                                <th><?=number_format($sum,2);?></th>

                            </tr>
                        </fhead>
                    </table>
                </div>
            </div>
                <center>
                    <?php
                   // echo \yii\widgets\LinkPager::widget(['pagination'=>$provider->pagination,]);
                    ?>
                </center>

            <!---span---->
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
                    <h5>Expenses</h5>
                </div>
                <div class="widget-content">
                    <table class="table table-bordered data-table">
                        <thead>
                        <tr>
                            <th><i class="icon-resize-vertical"></i>SNO</th>
                            <th>Date-Time</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sume = 0;
                        $count =1;
                        foreach($provider_expend AS $expend) {
                            ?>
                            <tr class="gradeX">
                                <td><center><?=$count;?></center></td>
                                <td><?=$expend['edate']; ?></td>
                                <td><?=$expend['ename']; ?></td>
                                <td><?=$expend['etype']; ?></td>
                                <td><?=number_format($expend['amount'],2);?></td>

                            </tr>
                            <?php
                            $sume += $expend['amount'];
                            $count++;
                        }
                        ?>
                        </tbody>
                        <fhead>
                            <tr>
                                <th><i class="icon-resize-vertical"></i>SNO</th>
                                <th>Date-Time</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th><?=number_format($sume,2);?></th>

                            </tr>
                        </fhead>
                    </table>
                </div>
            </div>

            <div class="widget-box">
                <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
                    <h5>Net Profits</h5>
                </div>
                <div class="widget-content">
                    <table class="table table-bordered data-table">
                        <thead>
                        <tr>
                            <th><i class="icon-resize-vertical"></i>SNO</th>
                            <th>Date-Time</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                       <tr>
                           <td></td>
                           <td></td>
                       </tr>
                        </tbody>
                        <fhead>
                            <tr>
                                <th><i class="icon-resize-vertical"></i>SNO</th>
                                <th>Date-Time</th>
                                <th><?=number_format($sum - $sume,2);?></th>

                            </tr>
                        </fhead>
                    </table>
                </div>
            </div>
        </div>
        <!--span-->
    </div>
</div>
