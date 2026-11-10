<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Items */

$this->title = "stock Details";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title"> <span class="icon">
            <input type="checkbox" id="title-checkbox" name="title-checkbox" />
            </span>
                <h5>Stock Details</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Name</th>
                        <th>Quantity-(Available)</th>
                        <th>Buying Price</th>
                        <th>Selling Price</th>
                        <th>VAT</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $count =1;
                    foreach($provider_items AS $items) { ?>
                        <tr>
                            <td><center><?=$count;?></center></td>
                            <td><?=$items['name']; ?></td>
                            <td><?=$items['quantity_t'];?></td>
                            <td><?=$items['bprice'];?></td>
                            <td><?=$items['sprice'];?></td>
                            <td><?=$items['vat'];?></td>

                        </tr>
                        <?php
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