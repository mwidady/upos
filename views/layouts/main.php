<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\bootstrap\Modal;
use yii\widgets\Breadcrumbs;
use kartik\dialog\DialogAsset;

AppAsset::register($this);
?>
<?php

DialogAsset::register($this);
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
<style>
.modal {
   background-color:transparent !important;
   border:transparent !important
}
.fade {
    opacity: 0;
    -webkit-transition: opacity .15s linear;
    -moz-transition: opacity .15s linear;
    -o-transition: opacity .15s linear;
    transition: opacity .15s linear;
}
</style>

</head>
<body>
<?php $this->beginBody();

Modal::begin([
   //'header' => '<span id="modalHeaderTitle">Berth Plan</span>',
     'headerOptions' => ['id' => 'modalHeader'],
     'id' => 'modal',
     'size' => 'modal-sm',   
     //keeps from closing modal with esc key or by clicking out of the modal.
     // user must click cancel or X to close
     //'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
     'clientOptions' => ['keyboard' => FALSE]
 ]);
 echo "<div id='modalContent'></div>";
 Modal::end();

    echo $this->render("header.php"); ?>
    <div id="content">
        <!--breadcrumbs-->
        <div id="content-header">
            <div id="breadcrumb" style="">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        </div>
</div>
<div id="content-body">
    <div class="col-md-12">
    <?= $content ?>
    </div>
       
</div>

<?php
echo $this->render("footer.php");
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
