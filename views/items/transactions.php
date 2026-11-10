<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>
<div class="container-fluid">
<div class="row-fluid">
    <div class="span12">
<div class="widget-box">
    <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
        <h5>Transactions</h5>
    </div>
    <div class="widget-content">
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th><i class="icon-resize-vertical"></i>SNO</th>
                <th>Name</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>VAT</th>
                <th>Date-Time</th>
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
                <td><?=$items['name']; ?></td>
                <td><?=$items['unit']; ?></td>
                <td><?=$items['sprice']; ?></td>
                <td><?=$items['quantity']; ?></td>
                <td><?=$items['quantity'] * $items['sprice'];?></td>
                <td><?=$items['vat']; ?></td>
                <td><?=$items['ts_time']; ?></td>
            </tr>
                <?php
                $sum += $items['quantity'] * $items['sprice'];
                $count++;
            }
            ?>
            </tbody>
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