<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="row-fluid">
    <div class="span12">


        <div class="widget-box">
            <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
                <h5>Invoices</h5>
            </div>
            <div class="widget-content">
                <table class="table table-bordered data-table">
                    <thead>
                    <tr>
                        <th><i class="icon-resize-vertical"></i>SNO</th>
                        <th>Date-Time</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>No of Items</th>
                        <th>Perfomed By</th>
                        <th>Details</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sum = 0;
                    $count =1;
                    foreach($provider_items AS $items) {
                        ?>
                        <tr class="gradeX">
                            <td><center><?=$count;?></center></td>
                            <td><?=$items['ts_time']; ?></td>
                            <td>
                                <center>
                                    <?=ucfirst($items['fname'])." ".ucfirst($items['lname'])." - ".$items['phone']." -- ".ucfirst($items['company']); ?>
                                </center>
                            </td>
                            <td><?=number_format($items['sumin'],2); ?></td>
                            <td><?=$items['nitem']; ?></td>
                            <td><?=ucfirst($items['first_name'])." ".ucfirst($items['last_name']); ?></td>
                            <td>
                                <center>
                                    <?= Html::a('<span>Details</span>', ['items/invdetails?inv_time='.$items['ts_time']], ['class'=>'btn btn-primary btn-mini']) ?>
                                </center>
                            </td>
                        </tr>
                        <?php
                        $sum += $items['sumin'];
                        $count++;
                    }
                    ?>
                    </tbody>
                    <fhead>
                        <tr>
                            <th><i class="icon-resize-vertical"></i></th>
                            <th>Date-Time</th>
                            <th>Customer</th>
                            <th><?=number_format($sum,2);?></th>
                            <th>No of Items</th>
                            <th>Perfomed By</th>
                            <th>Details</th>

                        </tr>
                    </fhead>
                </table>
            </div>
        </div>
    </div>
    <div class ="span12">
        <center>
            <?php
            echo \yii\widgets\LinkPager::widget([
                'pagination'=>$provider->pagination,
            ]);
            ?>
        </center>
    </div>
</div>
</div>