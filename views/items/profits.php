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
        <h5>Profits</h5>
    </div>
    <div class="widget-content">
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th><i class="icon-resize-vertical"></i>SNO</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Buying Price</th>
                <th>Buying Selling</th>

                <th>Total</th>
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
                <td><?=$items['quantity']; ?></td>
                <td><?=$items['bprice']; ?></td>
                <td><?=$items['sprice']; ?></td>

                <td><?=$items['quantity'] * $items['bprice'];?></td>
                <td><?=$items['quantity'] * $items['sprice'];?></td>

                <td><?=$items['ts_time']; ?></td>

                <td>
                    <?php
                  echo  $items['quantity'] * $items['sprice'] - $items['quantity'] * $items['sprice']
                    ?>
                </td>
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