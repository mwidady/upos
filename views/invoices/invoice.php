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
$this->registerCssFile("@web/css/style2.css");
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-briefcase"></i> </span>
                    <h5>POS - Invoice</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span12" style="width: 100%; float:left;">
                            <table class="table table-bordered table-invoice htable" style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <center>
                                                <div class="header1"> <?php echo  $station->station->name; ?></div>
                                                <div class="header2"> <?php echo  $station->station->description; ?></div>
                                                <div class="header3"> <b>Address:</b><?php echo  $station->station->address; ?></div>
                                                <div class="header4"> <b>Location:</b><?php echo  $station->station->location; ?></div>
                                                <div class="header5"> <b>Tel:</b><?php echo  $station->station->tel; ?></div>
                                                <div class="header6"> <b>Fax:</b><?php echo  $station->station->fax; ?></div>
                                                <div class="header7"> <b>Email:</b><?php echo  $station->station->email; ?></span>
                                            </center>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="branch">
                                            <div class="headerb"> <b>Branch Name:</b> <?php echo  $station->name; ?></div>
                                            <div class="headerb"> <b>Location:</b> <?php echo  $station->location; ?></div>
                                            <div class="headerb"> <b>Tel:</b> <?php echo  $station->tel; ?></span>
                                        </td>
                                        <td class="branch">
                                            <center>
                                                <strong>
                                                    Invoice No: <I><?= $receipt_details? $receipt_details['invoiceno'] :""; ?></I>
                                                </strong>
                                               <h5>
                                                  <?= $receipt_details?$receipt_details['status']:""; ?>
                                               </h5> 
                                                <br />
                                                <strong>
                                                    Issue Date: <?=$receipt_details? $receipt_details['pdatetime']:""; ?>
                                                </strong>
                                            </center>
                                        </td>
                                        <td class="branch">
                                            <h4>Customer Address:</h4>
                                            <strong><?php echo $receipt_details['company']; ?></strong> <br>
                                            <?php echo $receipt_details['fname'] . " " . $receipt_details['lname']; ?> <br>
                                            <?php echo $receipt_details['address']; ?>, <br>
                                            Contact No: <?php echo $receipt_details['phone']; ?> <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <table class="table table-bordered" style="width:100%;">
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
                                    $count = 1;
                                    foreach ($receipt_items as $items) {
                                    ?>
                                        <tr>
                                            <td>
                                                <center><?= $count; ?></center>
                                            </td>
                                            <td><?= $items['name']; ?></td>
                                            <td class="right"><?= $items['unit']; ?></td>
                                            <td class="right"><?= $items['quantity']; ?></td>
                                            <td class="right"><?= number_format($items['price'], 2); ?></td>
                                            <td class="right">
                                                <center>
                                                    <strong><?= number_format($items['quantity'] * $items['price'], 2); ?></strong>
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
                                            <center>
                                                <strong>
                                                    Tshs: <?= number_format($sum * 0.82, 2); ?> <br>
                                                    Tshs: <?= number_format($sum * 0.18, 2); ?> <br>
                                                </strong>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="pull-left" style="text-align: left">
                                <h6>Perfomed By: <?php echo ucfirst($receipt_details['first_name']); ?>&nbsp;<?php echo ucfirst($receipt_details['last_name']); ?></h6>
                            </div>
                            <div class="pull-right" style="text-align: right">
                                <h4><span>Amount Due: Tshs: </span> <?= number_format($sum, 2); ?></h4>
                                <br>
                                <?php if (!isset($showoftion)) {
                                    
                                    echo Html::a(
                                        '<span>Print Invoice</span>',
                                        ['items/invoiceprint?id='.$receipt_details['inv_id']],
                                        [
                                            'class' => 'btn btn-warning btn-small pull-left',
                                            'style' => 'margin-right:10px;',
                                           'target' => '_blank'
                                        ]
                                    );

                                    if ($receipt_details['status'] == 'NOT PAID') {
                                        echo Html::a(
                                            '<span>Cancel Invoice</span>',
                                            ['items/cancelinvoice?id=' . $receipt_details['inv_id']],
                                            [
                                                'class' => 'btn btn-danger btn-small pull-left',
                                                'style' => 'margin-right:10px;'
                                            ]
                                        );
                                    }

                                    // if($payPartial > 0){
                                        if ($receipt_details['status'] == 'NOT PAID' || $receipt_details['status'] == 'PAID PARTIAL') {
                                            echo Html::button('<span>Pay Invoice</span>',
                                            ['value' => Yii::$app->urlManager->createUrl(['items/paymodal/'.$receipt_details['inv_id']]),
                                            'class' => 'showModalButton btn btn-primary btn-sm',
                                            'data-placement' => 'bottom',
                                            'title' => 'Pay Invoice']
                                        );
                                        }
                                    // }
                                    // if($payPartial == 0){
                                    //     if ($receipt_details['status'] == 'NOT PAID') {
                                    //       echo Html::a('Pay Invoice', ['items/payfull', 'id' => $receipt_details['inv_id']], ['class' => 'btn btn-primary btn-sm']);
                                    //     }
                                    // }
                                    if ($receipt_details['status'] == 'PAID') {
                                        ?>
                                        <span class='btn-success btn-small pull-right'>
                                            Invoice Paid
                                        </span>
                                      <?php
                                    }
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