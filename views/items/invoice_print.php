<?php
use yii\helpers\Html;
use yii\helpers\Url;

$img = Url::to('@web/');
$this->title = 'Invoice';
$this->params['breadcrumbs'][] = $this->title;
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
                        <table class="invoice table-invoice htable" style="width: 100%">
                        <tbody>
                        <tr>
                        <td style="width:25%">
                        </td>
                        <td style="width:65%">
                            <center>
                                <div class="header1"> 
                                <strong><?php echo  $station->station->name; ?></strong>
                                </div>
                                <div class="header2"> <?php echo  $station->station->description; ?></div>
                                <div class="header3"> <b>Address:</b><?php echo  $station->station->address; ?></div>
                                <div class="header4"> <b>Location:</b><?php echo  $station->station->location; ?></div>
                                <div class="header5"> <b>Tel:</b><?php echo  $station->station->tel; ?></div>
                                <div class="header6"> <b>Fax:</b><?php echo  $station->station->fax; ?></div>
                                <div class="header7"> <b>Email:</b><?php echo  $station->station->email; ?></span>
                            </center>
                        </td>
                        <td style="width:10%">
                        </td>
                        </tr>
                        
                        <tr>
                        <td class="branch">
                            <div class="headerb">
                               <b>Branch Name:</b> <?php  echo  $station->name;?>
                            </div>
                            <div class="headerb"> <b>Location:</b> <?php  echo  $station->location;?></div>
                            <div class="headerb"> <b>Tel:</b> <?php  echo  $station->tel;?></span>   
                        </td>
                        <td class="branch">
                        <h4>Customer Address</h4>
                            <strong><?php echo $receipt_details['company'];?></strong> <br>
                            <?php echo $receipt_details['fname']." ".$receipt_details['lname'];?> <br>
                            <?php echo $receipt_details['address'];?>, <br>
                            Contact No: <?php echo $receipt_details['phone'];?> <br>
                        </td>
                        <td class="branch">
                        <center>
                           Invoice No: <br/>
                         <strong> 
                           <I><?=$receipt_details['invoiceno'];?></I>        
                         </strong>
                         <h5><?=$receipt_details['status'];?></h5>
                         <br/>
                            Issue Date: <br/>
                        <strong>
                            <?php echo $receipt_details['pdatetime'];?>
                        </strong>
                        </center>
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
                                    <th colspan="2" class="head1">Name</th>
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
                                        <td colspan="2"><?=$items['name']; ?></td>
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
                               
                                <tr>
                                  <td colspan="4"></td>
                                    <td colspan ="1" class="right"><strong>Subtotal</strong> <br>
                                        <strong>Tax (18%)</strong> <br>
                                    </td>
                                    <td colspan ="1" class="right">
                                             <strong>
                                               Tshs: <?=number_format($sum * 0.82,2);?> <br>
                                               Tshs: <?=number_format($sum * 0.18,2);?> <br>
                                            </strong>
                                    </td>
                                </tr>
                                <tr>
                                  <td style="text-align: left" colspan="4">
                                  <h6>Perfomed By: <?php echo ucfirst($receipt_details['first_name']);?>&nbsp;<?php echo ucfirst($receipt_details['last_name']);?></h6>
                                  </td>
                                  <td style="text-align: right" colspan="2">
                                <span>
                                    <b>
                                        Amount Due: Tshs: </span> <?= number_format($sum,2); ?>
                                    </b>
                                  </td>
                                </tr>
                                </tbody>
                            </table>
                               
                            <div class="pull-right" style="text-align: right">
                                
                                <?php if(!isset($showoftion)) { ?>
                                    <?= Html::a('<span>Print Invoice</span>', ['items/invoiceprint?id=' . $receipt_details['inv_id']], ['class' => 'btn btn-warning btn-small pull-left', 'target' => '_blank']) ?>
                                    &nbsp;
                                    <?= Html::button('<span>Pay Invoice</span>',
                                        ['value' => Yii::$app->urlManager->createUrl(['items/paymodal?id=' . $receipt_details['inv_id']]),
                                        'class' => 'showModalButton btn btn-primary btn-sm pull-right ',
                                        'title' => 'Pay Invoice']);
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
