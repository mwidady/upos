<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Items */
/* @var $previewRows array */
/* @var $importResults array */
/* @var $requiredColumns array */

$this->title = 'Upload Items';
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$autoFilledColumns = [
    ['db' => 'items.unit', 'value' => 'PCS'],
    ['db' => 'items.uid', 'value' => 'Current logged-in user'],
    ['db' => 'items.branch_id', 'value' => 'Current user branch'],
];
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="icon-upload"></i></span>
                    <h5>Upload Items</h5>
                </div>
                <div class="widget-content">
                    <p>The upload file must follow this exact spreadsheet order. These values will be saved into the database columns shown below.</p>
                    <p>
                        <?= Html::a(
                            '<i class="glyphicon glyphicon-download-alt"></i> Download Sample Template',
                            ['download-template'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </p>

                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Excel Column</th>
                            <th>Upload Title</th>
                            <th>Database Column</th>
                            <th>Required</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($requiredColumns as $excel => $column): ?>
                            <tr>
                                <td><?= Html::encode($excel) ?></td>
                                <td><?= Html::encode($column['label']) ?></td>
                                <td><code><?= Html::encode($column['database']) ?></code></td>
                                <td><?= Html::encode($column['required']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p>These database columns are filled automatically during upload and should not be included in the Excel file.</p>

                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Database Column</th>
                            <th>Value Used</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($autoFilledColumns as $column): ?>
                            <tr>
                                <td><code><?= Html::encode($column['db']) ?></code></td>
                                <td><?= Html::encode($column['value']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p><strong>Example order:</strong> Name, Description, Brand, Buying Price, Selling Price, Quantity.</p>
                    <p><strong>Quantity is optional.</strong> If supplied in column F, it must be a non-negative whole number and will be saved as the item's opening stock.</p>

                    <?php $form = ActiveForm::begin([
                        'options' => ['enctype' => 'multipart/form-data']
                    ]); ?>

                    <?= Html::hiddenInput('submitAction', 'preview') ?>
                    <?= $form->field($model, 'excelFile')->fileInput(['accept' => '.xls,.xlsx,.csv']) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Preview File', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Back to Items', ['index'], ['class' => 'btn btn-default']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($previewRows)): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-eye-open"></i></span>
                        <h5>Preview Before Upload</h5>
                    </div>
                    <div class="widget-content">
                        <p><?= count($previewRows) ?> row(s) found. Review the list before importing.</p>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Sheet Row</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Brand</th>
                                <th>Buying Price</th>
                                <th>Selling Price</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($previewRows as $row): ?>
                                <tr>
                                    <td><?= Html::encode($row['row_number']) ?></td>
                                    <td><?= Html::encode($row['name']) ?></td>
                                    <td><?= Html::encode($row['description']) ?></td>
                                    <td><?= Html::encode($row['brand']) ?></td>
                                    <td><?= Html::encode($row['bprice']) ?></td>
                                    <td><?= Html::encode($row['sprice']) ?></td>
                                    <td><?= Html::encode($row['quantity'] === '' ? 'Not supplied' : $row['quantity']) ?></td>
                                    <td><?= Html::encode($row['unit']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php $importForm = ActiveForm::begin(); ?>
                        <?= Html::hiddenInput('submitAction', 'import') ?>
                        <?= Html::submitButton('Import Items', ['class' => 'btn btn-success']) ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($importResults['rows'])): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-list-alt"></i></span>
                        <h5>Upload Results</h5>
                    </div>
                    <div class="widget-content">
                        <p>Imported: <strong><?= Html::encode($importResults['success_count']) ?></strong> | Failed: <strong><?= Html::encode($importResults['error_count']) ?></strong></p>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Sheet Row</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($importResults['rows'] as $row): ?>
                                <tr class="<?= $row['status'] === 'Failed' ? 'error' : 'success' ?>">
                                    <td><?= Html::encode($row['row_number']) ?></td>
                                    <td><?= Html::encode($row['name']) ?></td>
                                    <td><?= Html::encode($row['quantity'] === '' ? 'Not supplied' : $row['quantity']) ?></td>
                                    <td><?= Html::encode($row['status']) ?></td>
                                    <td><?= Html::encode($row['message']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
