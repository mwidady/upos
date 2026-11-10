<?php

namespace app\controllers;

use Yii;
use app\models\User;
use kartik\mpdf\Pdf;
use app\models\Items;
use yii\helpers\Json;
use yii\helpers\FileHelper;
use app\models\Batches;
use app\models\Workflow;


use yii\web\Controller;
use app\models\Invoices;
use app\models\Customers;

use app\models\WorkflowConstant;
use app\models\ItemsSearch;
use yii\filters\VerbFilter;
use app\models\Transactions;
use app\models\Quantity;
use app\models\QuantityTotal;
use yii\data\SqlDataProvider;
use yii\filters\AccessControl;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\PaymentTransactions;

use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



/**
 * ItemsController implements the CRUD actions for Items model.
 */
class ItemsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],

            ], 'access' => [
                'class' => AccessControl::className(),
                //'denyCallback' => function ($rule, $action) {
                //  throw new \Exception('You are not allowed to access this page');
                // },
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
 
    /**
     * Lists all Items models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Items model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Items model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Items();

        if ($model->load(Yii::$app->request->post())) {

            $branchId = User::findOne(Yii::$app->user->id)->branch_id;
            $model->branch_id = $branchId;
            $model->uid =  Yii::$app->user->id;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionUpload()
    {
        $model = new Items();
        $session = Yii::$app->session;
        $previewRows = $session->get('item_upload_preview_rows', []);
        $importResults = $session->getFlash('item_upload_results', []);

        if (Yii::$app->request->isPost) {
            $submitAction = Yii::$app->request->post('submitAction');

            if ($submitAction === 'preview') {
                $model->excelFile = UploadedFile::getInstance($model, 'excelFile');

                if ($model->validate(['excelFile']) && $model->excelFile) {
                    $previewRows = $this->parseUploadRows($model->excelFile->tempName);
                    $session->set('item_upload_preview_rows', $previewRows);

                    if (empty($previewRows)) {
                        $session->setFlash('error', 'No valid item rows were found in the selected file.');
                    } else {
                        $session->setFlash('success', 'Preview generated. Review the rows below, then click Import Items.');
                    }
                }
            }

            if ($submitAction === 'import') {
                $previewRows = $session->get('item_upload_preview_rows', []);

                if (empty($previewRows)) {
                    $session->setFlash('error', 'Please preview a file before importing.');
                } else {
                    $importResults = $this->importPreviewRows($previewRows);
                    $session->remove('item_upload_preview_rows');
                    $previewRows = [];
                }
            }
        }

        return $this->render('upload', [
            'model' => $model,
            'previewRows' => $previewRows,
            'importResults' => $importResults,
            'requiredColumns' => $this->getUploadColumnGuide(),
        ]);
    }

    public function actionDownloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Items Upload');

        $columnIndex = 1;
        foreach ($this->getUploadColumnGuide() as $columnDefinition) {
            $heading = is_array($columnDefinition) ? $columnDefinition['label'] : $columnDefinition;
            $column = Coordinate::stringFromColumnIndex($columnIndex++);
            $sheet->setCellValue($column . '1', $heading);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $lastColumn = Coordinate::stringFromColumnIndex(count($this->getUploadColumnGuide()));
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        ob_start();
        (new Xlsx($spreadsheet))->save('php://output');
        $content = ob_get_clean();
        $spreadsheet->disconnectWorksheets();

        return Yii::$app->response->sendContentAsFile(
            $content,
            'items-upload-template.xlsx',
            ['mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    private function getUploadColumnGuide()
    {
        return [
            'A' => ['label' => 'Item Name', 'database' => 'items.name', 'required' => 'Yes'],
            'B' => ['label' => 'Description', 'database' => 'items.description', 'required' => 'No'],
            'C' => ['label' => 'Brand', 'database' => 'items.brand', 'required' => 'No'],
            'D' => ['label' => 'Buying Price', 'database' => 'items.bprice', 'required' => 'Yes'],
            'E' => ['label' => 'Selling Price', 'database' => 'items.sprice', 'required' => 'Yes'],
            'F' => ['label' => 'Quantity', 'database' => 'quantity_total.quantity_t', 'required' => 'No'],
        ];
    }

    private function parseUploadRows($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $rows = $spreadsheet->getSheet(0)->toArray('', true, true, false);
        array_shift($rows);
        $previewRows = [];

        foreach ($rows as $index => $row) {
            if (empty(array_filter($row, function ($value) {
                return trim((string) $value) !== '';
            }))) {
                continue;
            }

            $previewRows[] = [
                'row_number' => $index + 2,
                'name' => trim((string) ($row[0] ?? '')),
                'description' => trim((string) ($row[1] ?? '')),
                'brand' => trim((string) ($row[2] ?? '')),
                'bprice' => trim((string) ($row[3] ?? '')),
                'sprice' => trim((string) ($row[4] ?? '')),
                'quantity' => trim((string) ($row[5] ?? '')),
                'unit' => 'PCS',
            ];
        }

        return $previewRows;
    }

    private function importPreviewRows($previewRows)
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $userId = Yii::$app->user->id;
        $results = ['success_count' => 0, 'error_count' => 0, 'rows' => []];

        foreach ($previewRows as $previewRow) {
            $quantityValue = trim((string) ($previewRow['quantity'] ?? ''));

            if ($quantityValue !== '' && !ctype_digit($quantityValue)) {
                $results['error_count']++;
                $results['rows'][] = $this->uploadResult($previewRow, $quantityValue, 'Failed', 'Quantity must be a non-negative whole number.');
                continue;
            }

            $item = new Items();
            $item->name = $previewRow['name'];
            $item->description = $previewRow['description'];
            $item->brand = $previewRow['brand'];
            $item->bprice = is_numeric($previewRow['bprice']) ? (float) $previewRow['bprice'] : null;
            $item->sprice = is_numeric($previewRow['sprice']) ? (float) $previewRow['sprice'] : null;
            $item->unit = $previewRow['unit'];
            $item->branch_id = $branchId;
            $item->uid = $userId;

            if (!$item->validate()) {
                $results['error_count']++;
                $results['rows'][] = $this->uploadResult($previewRow, $quantityValue, 'Failed', implode('; ', $this->flattenErrors($item->getErrors())));
                continue;
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if (!$item->save(false)) {
                    throw new \RuntimeException('The item could not be saved.');
                }

                if ($quantityValue !== '') {
                    $quantity = (int) $quantityValue;
                    $quantityTotal = new QuantityTotal([
                        'item_id' => $item->id,
                        'quantity_t' => $quantity,
                        'last_update' => date('Y-m-d H:i:s'),
                    ]);
                    $quantityLog = new Quantity([
                        'item_id' => $item->id,
                        'user_id' => $userId,
                        'quantity' => $quantity,
                        'q_date' => date('Y-m-d H:i:s'),
                    ]);

                    if (!$quantityTotal->save() || !$quantityLog->save()) {
                        $stockErrors = array_merge($quantityTotal->getErrors(), $quantityLog->getErrors());
                        throw new \RuntimeException(implode('; ', $this->flattenErrors($stockErrors)));
                    }
                }

                $transaction->commit();
                $results['success_count']++;
                $message = $quantityValue === ''
                    ? 'Item uploaded successfully without an opening quantity.'
                    : 'Item and opening quantity uploaded successfully.';
                $results['rows'][] = $this->uploadResult($previewRow, $quantityValue, 'Imported', $message);
            } catch (\Throwable $exception) {
                $transaction->rollBack();
                $results['error_count']++;
                $results['rows'][] = $this->uploadResult($previewRow, $quantityValue, 'Failed', $exception->getMessage());
            }
        }

        if ($results['success_count'] > 0) {
            Yii::$app->session->setFlash('success', $results['success_count'] . ' item(s) imported successfully.');
        }
        if ($results['error_count'] > 0) {
            Yii::$app->session->setFlash('error', $results['error_count'] . ' row(s) failed during import. Review the results below.');
        }

        return $results;
    }

    private function uploadResult($previewRow, $quantity, $status, $message)
    {
        return [
            'row_number' => $previewRow['row_number'],
            'name' => $previewRow['name'],
            'quantity' => $quantity,
            'status' => $status,
            'message' => $message,
        ];
    }

    private function flattenErrors($errors)
    {
        $messages = [];
        foreach ($errors as $attributeErrors) {
            foreach ($attributeErrors as $message) {
                $messages[] = $message;
            }
        }
        return $messages;
    }
    

    /**
     * Updates an existing Items model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionRemove($id)
    {
        $connection = \Yii::$app->db;
        $connection->createCommand()
            ->delete('transactions', 'id =' . $id)
            ->execute();
        return $this->redirect(['sales']);
    }
    public function actionRemoveall()
    {
        $connection = \Yii::$app->db;

        $connection->createCommand("DELETE FROM transactions WHERE user_id ='" . Yii::$app->user->id . "' AND status = 'unsold'")
            ->execute();
        return $this->redirect(['sales']);
    }

    public function actionAddquantity($id)
    {
        $check_item_id = Transactions::find()->where(['id' => $id])->one();

        $check_quantity = QuantityTotal::find()->where(['item_id' => $check_item_id->item_id])->one();

        if ($check_item_id->quantity == $check_quantity->quantity_t) {
            \Yii::$app->getSession()->setFlash('error', 'Product out of stock');
        } else {
            $connection = \Yii::$app->db;
            $connection->createCommand('UPDATE transactions SET quantity = quantity + 1 WHERE id=' . $id)
                ->execute();
        }


        return $this->redirect(['sales', 'show_customer' => '1']);
    }

    public function actionRemovequantity($id)
    {
        $connection = \Yii::$app->db;
        $connection->createCommand(
            'UPDATE transactions 
                SET quantity = quantity - 1 
                WHERE id=' . $id
        )
            ->execute();
        return $this->redirect(
            ['sales', 'show_customer' => '1']
        );
    }

    public function actionSell()
    {
        $modelc = new Customers();
        if ($modelc->load(Yii::$app->request->post())) {
            $customer_id = $_POST['Customers']['id'];
        }

        $ts_time = new \yii\db\Expression('NOW()');

        $connection = \Yii::$app->db;

        $modelcheck = $connection->createCommand(
            "SELECT * 
                FROM transactions 
                WHERE status ='unsold' 
                AND user_id=" . Yii::$app->user->id
        );
        $check_items = $modelcheck->queryAll();

        foreach ($check_items as $ckeck) {
            $connection->createCommand(
                'UPDATE transactions SET 
                    status = "invoice",
                    ts_time = ' . $ts_time . ',
                    customer_id =' . $customer_id . ' 
                    WHERE 
                    status= "unsold" 
                    AND item_id = ' . $ckeck['item_id'] . ' 
                    AND user_id=' . Yii::$app->user->id
            )
                ->execute();

            $connection->createCommand(
                "UPDATE quantity_total 
                    SET quantity_t = quantity_t - '" . $ckeck['quantity'] . "' 
                    WHERE  item_id = '" . $ckeck['item_id'] . "'"
            )
                ->execute();
        }


        //create invoice number

        //Get station ID
        $stationId = User::findOne(Yii::$app->user->id)
            ->branch
            ->station_id;

        //Get branch ID
        $branchId = User::findOne(Yii::$app->user->id)
            ->branch_id;


         $payPartial = Workflow::find()
         ->where(['branch_id' => $branchId,'workflow_code' => WorkflowConstant::PARTIAL_PAYMENT])
         ->count();   

        //get 2 batches min max
        $batch = Batches::find()
            ->where(['station_id' => $stationId])
            ->orderBy(['created_at' => SORT_DESC])->one();

        //check last invoice batch  number
        $invoice = Invoices::find()
            ->where(['branch_id' => $branchId])
            ->orderBy(['created_at' => SORT_DESC])->one();
        //get transaction amount total

        $query = (new \yii\db\Query())
            ->from('transactions')
            ->where(['ts_time' => $ts_time]);

        $sumtotal = $query->sum('price * quantity');

        //save invoice
        $inv = new Invoices();
        $inv->amount = $sumtotal;
        $inv->generated_by = Yii::$app->user->id;
        $inv->status = "NOT PAID";
        $inv->created_at = $ts_time;
        $inv->branch_id = $branchId;
        $inv->customer_id = $customer_id;

        if ($invoice == null) {
            $inv->invoiceno = $batch->start_at;
            $inv->save(false);

            \Yii::$app->db->createCommand(
                "UPDATE transactions SET 
                    invoice_id = $inv->id
                    WHERE ts_time = $ts_time"
            )
                ->execute();
        } else {
            $inv->invoiceno = $invoice->invoiceno + 1;
            $inv->save(false);

            \Yii::$app->db->createCommand(
                "UPDATE transactions SET 
                    invoice_id = $inv->id
                    WHERE ts_time = $ts_time"
            )
                ->execute();
        }

        $branchId = User::findOne(Yii::$app->user->id)->branch_id;

        $receipt_details = (new \yii\db\Query())
            ->select([
                'DISTINCT(T.ts_time) AS pdatetime', 'T.id', 'U.first_name', 'U.last_name',
                'C.clogo', 'I.status', 'C.fname', 'C.lname', 'C.phone', 'C.company',
                'C.address', 'I.invoiceno', 'I.id as inv_id'
            ])
            ->from('transactions T')
            ->innerJoin('customers C', 'T.customer_id = C.id')
            ->innerJoin('user U', 'T.user_id = U.id')
            ->innerJoin('invoices I', 'T.invoice_id = I.id')
            ->where([
                'T.status' => 'invoice',
                'T.invoice_id' => $inv->id,
                'I.branch_id' => $branchId
            ])
            ->orderBy('T.id DESC')
            ->one();

        $receipt_items = (new \yii\db\Query())
            ->select([
                'T.id', 'I.name', 'T.price',
                'T.quantity', 'I.supplier', 'I.vat',
                'I.unit', 'T.ts_time'
            ])
            ->from('items I')
            ->innerJoin('transactions T', 'T.item_id = I.id')
            ->where(
                [
                    'T.invoice_id' => $inv->id,
                    'T.status' => 'invoice',
                    'I.branch_id' => $branchId
                ]
            )
            ->all();


        $station = User::findOne(Yii::$app->user->id)->branch;

        return $this->render('//invoices/invoice', [
            'receipt_items' => $receipt_items,
            'receipt_details' => $receipt_details,
            'payPartial' => $payPartial,
            'station' => $station
        ]);
    }

    public function actionInvdetails($inv_time)
    {
        $ts_time = $inv_time;
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;

        $receipt_details = (new \yii\db\Query())
            ->select([
                'DISTINCT(T.ts_time) AS pdatetime', 'T.id', 'U.first_name', 'U.last_name',
                'C.clogo', 'I.status', 'C.fname', 'C.lname', 'C.phone', 'C.company',
                'C.address'
            ])
            ->from('transactions T')
            ->innerJoin('customers C', 'T.customer_id = C.id')
            ->innerJoin('user U', 'T.user_id = U.id')
            ->where([
                'T.status' => 'invoice',
                'T.ts_time' => $ts_time,
                'I.branch_id' => $branchId
            ])
            ->orderBy('T.id DESC')
            ->one();

        $receipt_items = (new \yii\db\Query())
            ->select([
                'T.id', 'I.name', 'T.price',
                'T.quantity', 'I.supplier', 'I.vat',
                'I.unit', 'T.ts_time'
            ])
            ->from('items I')
            ->innerJoin('transactions T', 'T.item_id = I.id')
            ->where(
                [
                    'T.ts_time' => $ts_time,
                    'T.status' => 'invoice',
                    'I.branch_id' => $branchId
                ]
            )
            ->all();

        return $this->render('receipt', [
            'receipt_items' => $receipt_items,
            'receipt_details' => $receipt_details,
        ]);
    }
    public function actionRecdetails($rec_time)
    {
        $ts_time = $rec_time;
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;

        $receipt_details = (new \yii\db\Query())
            ->select([
                'DISTINCT(T.ts_time) AS pdatetime', 'T.id', 'U.first_name', 'U.last_name',
                'C.clogo', 'I.status', 'C.fname', 'C.lname', 'C.phone', 'C.company',
                'C.address', 'I.invoiceno', 'I.id as inv_id'
            ])
            ->from('transactions T')
            ->innerJoin('customers C', 'T.customer_id = C.id')
            ->innerJoin('user U', 'T.user_id = U.id')
            ->innerJoin('invoices I', 'T.invoice_id = I.id')
            ->where([
                'I.status' => 'PAID',
                'T.ts_time' => $ts_time,
                'I.branch_id' => $branchId
            ])
            ->groupBy('T.ts_time')
            ->orderBy('T.id DESC')
            ->one();


        $receipt_items = (new \yii\db\Query())
            ->select([
                'T.id', 'I.name', 'T.price',
                'T.quantity', 'I.supplier', 'I.vat',
                'I.unit', 'T.ts_time'
            ])
            ->from('items I')
            ->innerJoin('transactions T', 'T.item_id = I.id')
            ->where(
                [
                    'T.ts_time' => $ts_time,
                    'I.branch_id' => $branchId
                ]
            )
            ->all();

        $query = (new \yii\db\Query())
            ->from('transactions')
            ->where(['ts_time' => $ts_time]);
        $modelsumt = $query->sum('price * quantity');

        $station = User::findOne(Yii::$app->user->id)->branch;

        return $this->render('receipt_final', [
            'receipt_items' => $receipt_items,
            'receipt_details' => $receipt_details,
            'station' => $station,
            'modelsumt' => $modelsumt
        ]);
    }

    public function actionPaymodal($id)
    {

        $invoice = Invoices::findOne($id);
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
         $payPartial = Workflow::find()
        ->where(['branch_id' => $branchId,'workflow_code' => WorkflowConstant::PARTIAL_PAYMENT])
        ->count();  
        

        $model = new PaymentTransactions();
        $remain_amount = 0;
        if ($model->load(Yii::$app->request->post())) {
            
            $amount_paid = $invoice->amount;
            $paidStatus = 'PAID';
            
            
            if($payPartial){
                if ($model->status == 'Partial') {
                    $paidStatus = 'PAID PARTIAL';
    
                    $query = (new \yii\db\Query())
                        ->from('payment_transactions')
                        ->where(['invoice_id' => $id]);
                    $totalPaid = $query->sum('amount_paid');
    
                    $remain_amount = $invoice->amount - ($totalPaid + $model->amount_paid);
            }
                if ($invoice->status == "PARTIAL PAID" && $model->status == "Full"){
    
                    $query3 = (new \yii\db\Query())
                        ->from('payment_transactions')
                        ->where(['invoice_id' => $id]);
                    $totalPaid3 = $query3->sum('amount_paid');
    
                    $amount_paid = $invoice->amount - $totalPaid3;
            }
            }

            $payment =  new PaymentTransactions();
            $payment->amount_paid = $amount_paid;
            $payment->amount_remain = $remain_amount;
            $payment->initial_amount = $invoice->amount;
            $payment->paid_through = $model->paid_through;
            $payment->account_no = "NONE";
            $payment->paid_by = $invoice->customer_id;
            $payment->received_by = Yii::$app->user->id;
            $payment->invoice_id = $invoice->id;
            $payment->paid_date = date('Y-m-d h:i:s');
            $payment->created_by = Yii::$app->user->id;
            $payment->created_at = date('Y-m-d h:i:s');

            $payment->save(false);

            $query2 = (new \yii\db\Query())
                ->from('payment_transactions')
                ->where(['invoice_id' => $id]);
            $totalPaid2 = $query2->sum('amount_paid');

            if ($totalPaid2 >= $invoice->amount) {
                $paidStatus = 'PAID';
            }

            $connection = \Yii::$app->db;

            $connection->createCommand(
                "UPDATE invoices SET 
                status = '" . $paidStatus . "'
                WHERE id = '" . $id . "'"
            )
                ->execute();

            $receipt_details = (new \yii\db\Query())
                ->select([
                    'T.ts_time',
                    'T.ts_time AS pdatetime',
                    'U.first_name', 'U.last_name', 'C.clogo',
                    'I.status', 'C.fname', 'C.lname', 'C.phone',
                    'C.company', 'C.address', 'I.invoiceno', 'I.id as inv_id'
                ])
                ->from('transactions T')
                ->innerJoin('customers C', 'T.customer_id = C.id')
                ->innerJoin('user U', 'T.user_id = U.id')
                ->innerJoin('invoices I', 'T.invoice_id = I.id')
                ->where([
                    'T.status' => 'invoice',
                    'T.invoice_id' => $id,
                    'I.branch_id' => $branchId
                ])
                ->groupBy(['T.id','T.ts_time','U.first_name', 'U.last_name', 'C.clogo','I.status', 'C.fname', 'C.lname', 'C.phone','C.company', 'C.address', 'I.invoiceno', 'I.id'])
                ->orderBy('T.id DESC')
                ->one();

            $station = User::findOne(Yii::$app->user->id)->branch;

            $query = (new \yii\db\Query())->from('transactions')->where(['invoice_id' => $id]);
            $modelsumt = $query->sum('price * quantity');


            $receipt_items = (new \yii\db\Query())
                ->select([
                    'T.id', 'I.name', 'T.price',
                    'T.quantity', 'I.supplier', 'I.vat',
                    'I.unit', 'T.ts_time'
                ])
                ->from('items I')
                ->innerJoin('transactions T', 'T.item_id = I.id')
                ->where(
                    [
                        'T.invoice_id' => $id,
                        'I.branch_id' => $branchId
                    ]
                )
                ->all();

            return $this->render('receipt_final', [
                'receipt_items' => $receipt_items,
                'receipt_details' => $receipt_details,
                'station' => $station,
                'modelsumt' =>  $modelsumt
            ]);
        }
        
        return $this->renderAjax('//invoices/pay_invoice', [
            'model' => $model,
            'payPartial' =>  $payPartial
        ]);
    }


    public function actionPayfull($id)
    {

        $invoice = Invoices::findOne($id);
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;

          
            $payment =  new PaymentTransactions();
            $payment->amount_paid = $invoice->amount;
            $payment->amount_remain =0;
            $payment->initial_amount = $invoice->amount;
            $payment->paid_through = "CASH";
            $payment->account_no = "NONE";
            $payment->paid_by = $invoice->customer_id;
            $payment->received_by = Yii::$app->user->id;
            $payment->invoice_id = $invoice->id;
            $payment->paid_date = date('Y-m-d h:i:s');
            $payment->created_by = Yii::$app->user->id;
            $payment->created_at = date('Y-m-d h:i:s');

            $payment->save(false);

            $connection = \Yii::$app->db;

            $connection->createCommand("UPDATE invoices SET status = 'PAID' WHERE id = '" . $id . "'")->execute();

            $receipt_details = (new \yii\db\Query())
                ->select([
                    'T.ts_time',
                    'T.ts_time AS pdatetime',
                    'U.first_name', 'U.last_name', 'C.clogo',
                    'I.status', 'C.fname', 'C.lname', 'C.phone',
                    'C.company', 'C.address', 'I.invoiceno', 'I.id as inv_id'
                ])
                ->from('transactions T')
                ->innerJoin('customers C', 'T.customer_id = C.id')
                ->innerJoin('user U', 'T.user_id = U.id')
                ->innerJoin('invoices I', 'T.invoice_id = I.id')
                ->where([
                    'T.status' => 'invoice',
                    'T.invoice_id' => $id,
                    'I.branch_id' => $branchId
                ])
                ->groupBy(['T.id','T.ts_time','U.first_name', 'U.last_name', 'C.clogo','I.status', 'C.fname', 'C.lname', 'C.phone','C.company', 'C.address', 'I.invoiceno', 'I.id'])
                ->orderBy('T.id DESC')
                ->one();

            $station = User::findOne(Yii::$app->user->id)->branch;

            $query = (new \yii\db\Query())->from('transactions')->where(['invoice_id' => $id]);
            $modelsumt = $query->sum('price * quantity');


            $receipt_items = (new \yii\db\Query())
                ->select([
                    'T.id', 'I.name', 'T.price',
                    'T.quantity', 'I.supplier', 'I.vat',
                    'I.unit', 'T.ts_time'
                ])
                ->from('items I')
                ->innerJoin('transactions T', 'T.item_id = I.id')
                ->where(
                    [
                        'T.invoice_id' => $id,
                        'I.branch_id' => $branchId
                    ]
                )
                ->all();

            return $this->render('receipt_final', [
                'receipt_items' => $receipt_items,
                'receipt_details' => $receipt_details,
                'station' => $station,
                'modelsumt' =>  $modelsumt
            ]);
     
    }


    public function actionPayinvoice($id)
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;

        $connection = \Yii::$app->db;

        $connection->createCommand(
            "UPDATE invoices 
            SET 
            status = 'PAID' 
            WHERE id = '" . $id . "'"
        )->execute();

        $receipt_details = (new \yii\db\Query())
        ->select([
            'DISTINCT(T.ts_time) AS pdatetime', 'T.id', 'U.first_name', 'U.last_name',
            'C.clogo', 'I.status', 'C.fname', 'C.lname', 'C.phone', 'C.company',
            'C.address', 'I.invoiceno', 'I.id as inv_id'
        ])
        ->from('transactions T')
        ->innerJoin('customers C', 'T.customer_id = C.id')
        ->innerJoin('user U', 'T.user_id = U.id')
        ->innerJoin('invoices I', 'T.invoice_id = I.id')
        ->where([
            'I.status' => 'PAID',
            'T.invoice_id' => $id,
            'I.branch_id' => $branchId
        ])
        ->groupBy('T.ts_time')
        ->orderBy('T.id DESC')
        ->one();

        $station = User::findOne(Yii::$app->user->id)->branch;

        $query = (new \yii\db\Query())
        ->from('transactions')
        ->where(['invoice_id' => $id]);
        $modelsumt = $query->sum('price * quantity');


        $receipt_items = (new \yii\db\Query())
        ->select([
            'T.id', 'I.name', 'T.price',
            'T.quantity', 'I.supplier', 'I.vat',
            'I.unit', 'T.ts_time'
        ])
        ->from('items I')
        ->innerJoin('transactions T', 'T.item_id = I.id')
        ->where(
            [
                'T.invoice_id' => $id,
                'I.branch_id' => $branchId
            ]
        )
        ->all();

        return $this->render('receipt_final', [
            'receipt_items' => $receipt_items,
            'receipt_details' => $receipt_details,
            'station' => $station,
            'modelsumt' =>  $modelsumt
        ]);
    }

    public function actionCancelinvoice($id)
    {
        $connection = \Yii::$app->db;

        $connection->createCommand(
            "UPDATE invoices 
            SET 
            status = 'CANCELLED' 
            WHERE id = '" . $id . "'"
        )->execute();
        return $this->redirect(['/invoices/index']);
    }

    public function actionCdetails($id)
    {
        $customer_d = Customers::find()->where(['id' => $id])->one();
?>
        <table class="table table-bordered table-striped">

            <tr>
                <td colspan="2">
                    <center>
                        <?php
                        if (!file_exists('@web/uploads/' . $customer_d['clogo'])) {
                        ?>
                            <img src="<?= Yii::getAlias('@web') . '/uploads/pos_customer_logo.png'; ?>" style="width:100px; height:50px;">
                        <?php
                        } else {
                        ?>
                            <img src="<?= Yii::getAlias('@web') . '/uploads/' . $customer_d['clogo']; ?>" style="width:100px; height:50px;">
                        <?php
                        }
                        ?>
                    </center>
                </td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td><?= $customer_d['fname'] . " " . $customer_d['lname']; ?></td>
            </tr>
            <tr>
                <th>Company</th>
                <td><?= $customer_d['company']; ?></td>
            </tr>
        </table>
<?php
    }
    public function actionSales()
    {
        $userid =  Yii::$app->user->id;
        $connection = \Yii::$app->db;
        $sql =  "SELECT T.id, I.name,I.sprice,T.quantity,I.supplier,I.vat 
        FROM items I,transactions T 
        WHERE I.id = T.item_id 
        AND T.status ='unsold' 
        AND T.user_id = '" . $userid . "' ";
        $count =  "SELECT COUNT(T.id) AS id, 
        I.name,I.sprice,T.quantity,I.supplier,I.vat 
        FROM items I,transactions T 
        WHERE I.id = T.item_id 
        AND T.status ='unsold' 
        AND T.user_id = '" . $userid . "' ";

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'key' => 'id',
            'totalCount' => $count,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => Transactions::find()
            ->where(['status' => 'unsold', 'user_id' => $userid]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $provider_items = $dataProvider->getModels();

        $model = new Items();
        $modelt = new Transactions();
        $customer = new Customers();
        
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        
        $payPartial = Workflow::find()
         ->where(['branch_id' => $branchId,'workflow_code' => WorkflowConstant::PARTIAL_PAYMENT])
         ->count();  

        if (Yii::$app->request->post('hasEditable')) {
            $out = Json::encode(['output' => "Success", 'message' => 'Ok']);
            // instantiate your book model for saving
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            //$bookId =
            $bookId = Yii::$app->request->post('editableKey');

            // $bookId = 195;
            $modeltt = Transactions::findOne($bookId);

            // store a default json response as desired by editable
            //  $out = Json::encode(['output'=>'', 'message'=>'']);

            // fetch the first entry in posted data (there should only be one entry
            // anyway in this array for an editable submission)
            // - $posted is the posted data for Book without any indexes
            // - $post is the converted array for single model validation

            $posted = current($_POST['Transactions']);
            $post = ['Transactions' => $posted];
            // load model like any single model validation
            // if ($modeltt->load($post)) {
            if ($modeltt->load($post)) {
                //  $modeltt->save(false);
                if (isset($posted['quantity'])) {
                    $output = $posted['quantity'];
                    $check_quantity = QuantityTotal::find()->where(['item_id' => $modeltt->item_id])->one();

                    if ($output > $check_quantity->quantity_t) {
                        $out = Json::encode(['output' => 'OL', 'message' => 'Quantity out of stock']);
                    } else {
                        $connection = \Yii::$app->db;
                        $connection->createCommand("UPDATE transactions SET quantity = '" . $output . "' WHERE id = '" . $bookId . "'")
                            ->execute();
                        // custom output to return to be displayed as the editable grid cell
                        // data. Normally this is empty - whereby whatever value is edited by
                        // in the input by user is updated automatically.


                        // specific use case where you need to validate a specific
                        // editable column posted when you have more than one
                        // EditableColumn in the grid view. We evaluate here a
                        // check to see if buy_amount was posted for the Book model

                        // similarly you can check if the name attribute was posted as well
                        // if (isset($posted['name'])) {
                        // $output = ''; // process as you need
                        // }
                        //$out = json_encodeJSON(['output'=>'OK','message'=> 'ok']);
                        //echo $out;
                    }
                }


                if (isset($posted['price'])) {
                    $priceEdit = $posted['price'];
                    $connection->createCommand("UPDATE transactions SET
                    price = '" . $priceEdit . "' WHERE id ='" . $bookId . "'")
                        ->execute();
                    // $out = Json::encode(['output' => $output, 'message' => 'ok']);
                }
                // echo $out;
            }
            // return ajax json encoded response and exit

            return  $this->redirect('sales?show_customer=1');
        }
        if ($model->load(Yii::$app->request->post())) {


            $check_exist_item = Items::find()->where(['id' => $model->name])->count();
            $check_exist_item_curent_sales = Transactions::find()
                ->where(['item_id' => $model->name, 'user_id' => Yii::$app->user->id, 'status' => 'unsold'])->count();

            if ($check_exist_item_curent_sales > 0) {
                // //\Yii::$app->getSession()->setFlash('error','No product with such number found');

            } else if ($check_exist_item < 1) {
                \Yii::$app->getSession()->setFlash('error', 'No product with such number found');
                // print("no product found");
            } else {
                $check_sold_price = Items::find()
                ->where(['id' => $model->name])
                ->one();
                $modelt->user_id = Yii::$app->user->id;
                $modelt->trans_date = new \yii\db\Expression('NOW()');
                $modelt->price = $check_sold_price->sprice;
                $modelt->customer_id = 1;
                $modelt->item_id = $model->name;
                $modelt->payment_type = 1;
                $modelt->quantity = 1;
                $modelt->status = "unsold";
                $modelt->ts_time = new \yii\db\Expression('NOW()');
                $modelt->transaction_no = rand(100, 10000);
                $modelt->save(false);
            }

            return $this->redirect(['sales', 'provider_items' => $provider_items, 'dataProvider' => $dataProvider, 'show_customer' => '1',]);
        } else {

            return $this->render('sales', [
                'model' => $model,
                'customer' => $customer,
                'dataProvider' => $dataProvider, 
                'provider_items' => $provider_items,
                'payPartial' => $payPartial
            ]);
        }
    }
    public function actionTransactions()
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $connection = \Yii::$app->db;
        $sql = "SELECT 
        T.id, I.name,I.sprice,T.quantity,I.supplier,
        I.vat,I.unit,T.status,T.ts_time 
        FROM items I,transactions T 
        WHERE I.id = T.item_id 
        AND T.status != 'unsold' 
        AND I.branch_id = '".$branchId."'";
        $model = $connection->createCommand($sql);
        $provider_itemsa = $model->queryAll();

        $count = sizeof($provider_itemsa);


$dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('transactions', [
            'provider_items' => $dataProvider->getModels(), 'provider' => $dataProvider
        ]);
    }
    public function actionProfits()
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $connection = \Yii::$app->db;
        $sql = "SELECT T.id, I.name,I.sprice,I.bprice,T.quantity,I.supplier,
        I.vat,I.unit,T.status,T.ts_time FROM items I,transactions T 
        WHERE I.id = T.item_id
        AND I.branch_id = '".$branchId."'";
        $model = $connection->createCommand($sql);
        $provider_itemsa = $model->queryAll();

        $count = sizeof($provider_itemsa);


        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('profits', [
            'provider_items' => $dataProvider->getModels(), 'provider' => $dataProvider
        ]);
    }
    public function actionProfitsn()
    {

        $modelt = new Transactions();
        $connection = \Yii::$app->db;
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;

        if (isset($_POST['Transactions']['ts_time'])) {

            $sdate = explode(' To ', $_POST['Transactions']['ts_time']);

            $sql1 = "SELECT T.id, I.name,I.bprice,T.price,T.quantity,I.supplier,I.unit,T.status,T.ts_time 
            FROM items I,transactions T,invoices II 
            WHERE I.id = T.item_id  
            AND II.id = T.invoice_id
            AND DATE(T.ts_time) BETWEEN '" . $sdate[0] . "' AND '" . $sdate[1] . "'
            AND II.status='PAID' AND
            I.branch_id = '".$branchId."'";

            $sql2 = "SELECT ename,etype,edate,amount,company,phone 
            FROM expenditures 
            WHERE 
            edate BETWEEN '" . $sdate[0] . "' AND '" . $sdate[1] . "'
            AND branch_id = '".$branchId."'";

            $pdate = $_POST['Transactions']['ts_time'];
        } else {
            $date_today = date('Y-m-d');

            $sql1 = "SELECT T.id, I.name,I.bprice,T.price,T.quantity,I.supplier,I.unit,T.status,T.ts_time 
            FROM items I,transactions T,invoices II 
            WHERE I.id = T.item_id
            AND II.id = T.invoice_id
            AND DATE(T.ts_time) = '" . $date_today . "'
            AND II.status='PAID' AND
            I.branch_id = '".$branchId."'";

            $sql2 = "SELECT ename,etype,edate,amount,company,phone
             FROM expenditures WHERE edate = '" . $date_today . "' 
             AND branch_id = '".$branchId."'";
            $pdate = 0;
        }
        $model = $connection->createCommand($sql1);
        $provider_trans = $model->queryAll();

        $model2 = $connection->createCommand($sql2);
        $provider_expend = $model2->queryAll();

        return $this->render('profitsn', [
            'provider_trans' => $provider_trans,
            'model' => $modelt,
            'pdate' => $pdate,
            'provider_expend' => $provider_expend,
        ]);
    }




    public function actionInvoices()
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $connection = \Yii::$app->db;
        $sql = "SELECT ts_time,T.item_id AS nitem,
        U.first_name,
        U.last_name,
        C.fname,
        C.lname,C.phone,C.company,T.status,
        SUM(T.price * T.quantity) AS sumin
        FROM items I,user U,transactions T 
        LEFT JOIN customers C ON C.id=T.customer_id  
        WHERE I.id = T.item_id 
        AND T.user_id = U.id 
        AND T.status = 'invoice' 
        AND I.branch_id = '".$branchId."'
        GROUP BY ('ts_time')";

        $model = $connection->createCommand($sql);
        $provider_itemsa = $model->queryAll();
        $count = sizeof($provider_itemsa);


        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('invoices', [
            'provider_items' => $dataProvider->getModels(), 'provider' => $dataProvider
        ]);
    }



    public function actionReceipts()
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $connection = \Yii::$app->db;
        $sql = "SELECT 
        T.ts_time,C.fname,C.lname,C.phone,C.company,U.first_name,
        U.last_name,V.invoiceno,
        SUM(T.price * T.quantity) AS sumin, COUNT(T.item_id) 
        AS nitem
        FROM items I,invoices V,user U,transactions T
        LEFT JOIN customers C ON C.id=T.customer_id  
        WHERE I.id = T.item_id 
        AND U.id = T.user_id 
        AND V.id = T.invoice_id 
        AND V.status = 'PAID'
        AND I.branch_id = '".$branchId."'
        GROUP BY T.ts_time";
        $model = $connection->createCommand($sql);
        $provider_itemsa = $model->queryAll();


        $count = sizeof($provider_itemsa);


        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('receipts', [
            'provider_items' => $dataProvider->getModels(),
            'provider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Items model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionInvoiceprint($id)
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $receipt_details = (new \yii\db\Query())
        ->select([
            'DISTINCT(T.ts_time) AS pdatetime', 'T.id', 'U.first_name', 'U.last_name',
            'C.clogo', 'I.status', 'C.fname', 'C.lname', 'C.phone', 'C.company',
            'C.address', 'I.invoiceno', 'I.id as inv_id','I.status'
        ])
        ->from('transactions T')
        ->innerJoin('customers C', 'T.customer_id = C.id')
        ->innerJoin('user U', 'T.user_id = U.id')
        ->innerJoin('invoices I', 'T.invoice_id = I.id')
        ->where([
            'T.invoice_id' => $id,
            'I.branch_id' => $branchId
        ])
        ->orderBy('T.id DESC')
        ->one();


        $receipt_items = (new \yii\db\Query())
        ->select([
            'T.id', 'I.name', 'T.price',
            'T.quantity', 'I.supplier', 'I.vat',
            'I.unit', 'T.ts_time'
        ])
        ->from('items I')
        ->innerJoin('transactions T', 'T.item_id = I.id')
        ->where(
            [
                'T.invoice_id' => $id,
                'I.branch_id' => $branchId
            ]
        )
        ->all();

        $station = User::findOne(Yii::$app->user->id)->branch;
        $content = $this->renderPartial('invoice_print', [
            'receipt_items' => $receipt_items,
            'receipt_details' => $receipt_details,
            'showoftion' => 1,
            'station' => $station
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            // 'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting

            //'cssFile' => '@webroot/css/bootstrap.css',
            'cssFile' => '@webroot/css/style2.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{} .htable td{} .htable{}',
            // set mPDF properties on the fly
            'options' => ['title' => 'POS INVOICE'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ['POS'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }
    public function actionReceiptprint($id)
    {

        $branchId = User::findOne(Yii::$app->user->id)->branch_id;

        $receipt_details = (new \yii\db\Query())
        ->select([
            'T.ts_time AS pdatetime', 'T.id', 'U.first_name', 'U.last_name',
            'C.clogo', 'I.status', 'C.fname', 'C.lname', 'C.phone', 'C.company',
            'C.address', 'I.invoiceno', 'I.id as inv_id'
        ])
        ->from('transactions T')
        ->innerJoin('customers C', 'T.customer_id = C.id')
        ->innerJoin('user U', 'T.user_id = U.id')
        ->innerJoin('invoices I', 'T.invoice_id = I.id')
        ->where([
            'T.invoice_id' => $id,
            'I.branch_id' => $branchId
        ])
        ->groupBy(['T.id','T.ts_time', 'T.ts_time', 'T.id', 'U.first_name', 'U.last_name',
        'C.clogo', 'I.status', 'C.fname', 'C.lname', 'C.phone', 'C.company',
        'C.address', 'I.invoiceno', 'I.id'])
        ->orderBy('T.id DESC')
        ->one();

        $query = (new \yii\db\Query())->from('transactions')
        ->where(['invoice_id' => $id]);
        $modelsumt = $query->sum('price * quantity');

        $receipt_items = (new \yii\db\Query())
        ->select([
            'T.id', 'I.name', 'T.price',
            'T.quantity', 'I.supplier', 'I.vat',
            'I.unit', 'T.ts_time'
        ])
        ->from('items I')
        ->innerJoin('transactions T', 'T.item_id = I.id')
        ->where(
            [
                'T.invoice_id' => $id,
                'I.branch_id' => $branchId
            ]
        )
        ->all();

        $station = User::findOne(Yii::$app->user->id)->branch;


        // $pdf = Yii::$app->pdf;
        //$pdf->content = $this->renderPartial('receipt', [
        $content = $this->renderPartial('receipt_final', [
            'receipt_items' => $receipt_items,
            'receipt_details' => $receipt_details,
            'station' => $station,
            'modelsumt' => $modelsumt,
            'showoftion' => 1,
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting


            // 'cssFile' => '@webroot/css/bootstrap.min.css',
            'cssFile' => '@webroot/css/style.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:10px} .htable td{border:1px solid #f5f5f5;} .htable{border:1px solid #f5f5f5; margin-left:5x;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'POS RECEIPT'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ['POS'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the Items model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Items the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Items::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
