<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$img = Url::to('@web/');
$this->title = 'Invoice';
$this->params['breadcrumbs'][] = $this->title;

//$this->registerCssFile("@webroot/css/bootstrap.min.css");
//$this->registerCssFile("@webroot/css/bootstrap-responsive.min.css");
//$this->registerCssFile("@webroot/css/matrix-style.css");
//$this->registerCssFile("@webroot/css/css/matrix-media.css");
//$this->registerCssFile("@webroot/font-awesome/css/font-awesome.css");
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-briefcase"></i> </span>
                    <h5 >K POS - Invoice</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span6" style="width: 48%; float:left;">
                            <table class="table table-bordered table-invoice htable" style="width: 100%">
                                <tbody>
                                <tr>
                                    <td><h4>POS</h4></td>
                                </tr>
                                <tr>
                                    <td>Dar es salaam</td>
                                </tr>
                                <tr>
                                    <td>Mobile Phone: +4530422244</td>
                                </tr>
                                <tr>
                                    <td>Email: me@kpos.com</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="span6" style="width: 49%; float:left;">
                            <table class="table table-bordered table-invoice htable" style="width: 100%">
                                <tbody>
                                <tr>
                                <tr>
                                    <td class="width30">Invoice No:</td>
                                    <td class="width70"><strong><?= preg_replace('/\s+/', '', $receipt_details['pdatetime']);?></strong></td>
                                </tr>
                                <tr>
                                    <td>Issue Date:</td>
                                    <td><strong><?php echo $receipt_details['pdatetime'];?></strong></td>
                                </tr>
                                <tr>
                                <td class="width30">Client Address:</td>
                                <td class="width70">
                                    <table class="htable" style="width:100%; border:0px solid #fff">
                                        <tr>
                                            <td>
                                                <strong><?php echo $receipt_details['company'];?></strong> <br>
                                               <?php echo $receipt_details['fname']." ".$receipt_details['lname'];?> <br>
                                                <?php echo $receipt_details['address'];?>, <br>
                                                Contact No: <?php echo $receipt_details['phone'];?> <br>
                                            </td>
                                        </tr>

                                    </table>

                                   </td>
                                </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <table class="table table-bordered"  style="width:100%;">
                                <thead>
                                <tr>
                                    <th class="head0">SN</th>
                                    <th class="head1">Name</th>
                                    <th class="head0 right">Unit</th>
                                    <th class="head0 right">Qty</th>
                                    <th class="head1 right">Price</th>
                                    <th class="head0 right">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sum = 0;
                                $count =1;
                                foreach($receipt_items AS $items) {
                                    ?>
                                    <tr>
                                        <td><center><?=$count;?></center></td>
                                        <td><?=$items['name']; ?></td>
                                        <td class="right"><?=$items['unit']; ?></td>
                                        <td class="right"><?=$items['quantity']; ?></td>
                                        <td class="right"><?=number_format($items['price'],2); ?></td>
                                        <td class="right">
                                           <center>
                                               <strong><?=number_format($items['quantity'] * $items['price'],2);?></strong>
                                           </center>
                                        </td>
                                    </tr>
                                    <?php
                                    $sum += $items['quantity'] * $items['price'];
                                    $count++;
                                }
                                ?>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-invoice-full">
                                <tbody>
                                <tr>
                                    <td class="msg-invoice" width="70%">

                                    </td>
                                    <td class="right"><strong>Subtotal</strong> <br>
                                        <strong>Tax (18%)</strong> <br>
                                    <td class="right">
                                             <strong>
                                               Tshs: <?=number_format($sum * 0.82,2);?> <br>
                                               Tshs: <?=number_format($sum * 0.18,2);?> <br>
                                            </strong>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                                <div class="pull-left" style="text-align: left">
                                    <h6>Perfomed By: <?php echo ucfirst($receipt_details['first_name']);?>&nbsp;<?php echo ucfirst($receipt_details['last_name']);?></h6>
                                </div>
                            <div class="pull-right" style="text-align: right">
                                <h4><span>Amount Due: Tshs: </span> <?= number_format($sum,2); ?></h4>
                                <br>
                                <?php if(!isset($showoftion)) { ?>
                                    <?= Html::a('<span>Print Invoice</span>', ['items/invoiceprint?inv_pid=' . $receipt_details['pdatetime']], ['class' => 'btn btn-warning btn-small pull-left', 'target' => '_blank']) ?>
                                    &nbsp;
                                    <?= Html::a('<span>Pay Invoice</span>', ['items/payinvoice?ts_time=' . $receipt_details['pdatetime']], ['class' => 'btn btn-primary btn-small pull-right']) ?>
                                    <?php
                                }
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
