<?php
if(!isset($showoftion)) {
?>
<style>
    .station,.receiptheader{
        text-align:center !important;
    }
</style>
<?php
}
?>

<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$img = Url::to('@web/');

$this->title = 'Receipt';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"> <i class="icon-briefcase"></i> </span>
                    <h5>POS - Receipt</h5>
                </div>
                <div class="widget-content">
                    <div class="row-fluid">
            <div class="receiptpage" style="">
                <table class="table table-bordered table-invoice htable">
                <tbody>
                <tr>
                <td style="width: 20%;">
                <img style="width:100%;" src="<?=Yii::getAlias('@web').'/uploads/stations/'.$station->station->slogo;?>"/>
                </td>
                <td colspan="4" class="station" style="width: 60%;">
                   <div class="header1"> <?php echo  $station->station->name; ?></div>
                   <div class="header2"> <?php echo  $station->station->description; ?></div>
                   <div class="header3"> <b>Address:</b><?php echo  $station->station->address; ?></div>
                   <div class="header4"> <b>Location:</b><?php echo  $station->station->location; ?></div>
                   <div class="header5"> <b>Tel:</b><?php echo  $station->station->tel; ?></div>
                   <div class="header6"> <b>Fax:</b><?php echo  $station->station->fax; ?></div>
                   <div class="header7"> <b>Email:</b><?php echo  $station->station->email; ?></span>
                </td>
                <td style="width: 20%;">
                </td>
                </tr>

                <tr>
                <td class="branch" colspan="2">
                    <div class="headerb"> <b>Branch Name:</b> <?php  echo  $station->name;?></div>
                    <div class="headerb"> <b>Location:</b> <?php  echo  $station->location;?></div>
                    <div class="headerb"> <b>Tel:</b> <?php  echo  $station->tel;?></span>
                </td>
                <td class="receiptheader" colspan="2">
                      <h1>Receipt</h1>
                </td>
                <td class="invoicenoheader" colspan="2">
              <center>
            <strong>INVOICE #</strong> <?= $receipt_details['invoiceno']?><br>
                <?= preg_replace('/\s+/', '', $receipt_details['pdatetime']);?></strong>

              </center> 
                
                </td>
                </tr>

                <tr style="height:80px;">
                <td  class="amountinwords" colspan="3">
                <strong>Payment Received from</strong>
                </td>
                <td colspan="3">
                <strong>Customer</strong><br/>
                <strong><?php echo $receipt_details['company'];?></strong> <br>
                <?php echo $receipt_details['fname']." ".$receipt_details['lname'];?> <br>
                <?php echo $receipt_details['address'];?>, <br>
                 Contact No: <?php echo $receipt_details['phone'];?> <br>
                </td>
                </tr>
                <tr>
                <td colspan="6">
                &nbsp;
                </td>
                </tr>
                <tr>
                <td colspan="6">
                &nbsp;
                </td>
                </tr>
                <tr>
                <td colspan="6">
                <center>
                    <h4>ITEM(S)</h4>
                </center>
                </td>
                </tr>
                <tr>
                <th class="head0">Sno</th>
                <th colspan="2" class="head1">Being Payment of</th>
                <th class="head0 right">Quantity</th>
                <th class="head1 right">Price</th>
                <th class="head0 right">Amount</th>
                </tr>
                <tbody>
                <?php
                $sum = 0;
                $count =1;
                foreach($receipt_items AS $items) {?>
                <tr>
                <td><center><?=$count;?></center></td>
                <td colspan="2"><?=$items['name']; ?></td>
                <td class="right"><?=$items['quantity']; ?></td>
                <td class="right"><?=number_format($items['price'],2); ?></td>
                <td><center>
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
                <td colspan="5"><strong>Total Amount Paid</strong></td>
                <td colspan="1">
                <center>
                <strong><?=number_format($sum,2);?></strong>
                </center>
                </td>
                </tr>
                <tr>
                <td colspan="6">
                &nbsp;
                </td>
                </tr>
                <tr>
                <td colspan="3">
                  Perfomed By:
               <strong>
                   <?php echo ucfirst($receipt_details['last_name']);?>
               </strong> 
                </td>
                <td style="text-align:right" colspan="3">
                    Amount Due: Tshs: 
                    <strong> 
                        <?=number_format($sum,2);?>

                      </strong>
                </td>
                </tr>
                </tbody>
                </table>
                <div class="pull-right" style="text-align: right;">
                    <?php
                    if(!isset($showoftion)) {
                        echo Html::a(
                            '<span>Print Receipt</span>',
                            ['items/receiptprint?id='.$receipt_details['inv_id']],
                             ['class' => 'btn btn-primary btn-small pull-right', 'target' => '_blank']);
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
