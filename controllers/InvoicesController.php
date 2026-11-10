<?php

namespace app\controllers;

use Yii;
use app\models\Transactions;
use app\models\Batches;
use app\models\Branch;
use app\models\Quantity;
use app\models\QuantityTotal;
use app\models\Invoices;
use app\models\Items;
use app\models\Customers;
use app\models\User;
use app\models\InvoicesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\Workflow;
use app\models\WorkflowConstant;

/**
 * InvoicesController implements the CRUD actions for Invoices model.
 */
class InvoicesController extends Controller
{
    
    public $enableCsrfValidation = false;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Invoices models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoicesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    

    /**
     * Displays a single Invoices model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
     $branchId = User::findOne(Yii::$app->user->id)->branch_id;
    $receipt_details = (new \yii\db\Query())
    ->select([
    'DISTINCT(T.ts_time) AS pdatetime',
    'T.id',
    'U.first_name','U.last_name','C.clogo',
    'I.status','C.fname', 'C.lname', 'C.phone',
    'C.company','C.address','I.invoiceno',
    'I.id as inv_id'
    ])
    ->from('transactions T')
    ->innerJoin('customers C','T.customer_id = C.id')
    ->innerJoin('user U','T.user_id = U.id')
    ->innerJoin('invoices I','T.invoice_id = I.id')
    ->where([
        'T.status'=>'invoice',
        'T.invoice_id'=> $id,
        'I.branch_id' => $branchId
        ])
        ->orderBy('T.id DESC')
    ->one();


    $receipt_items = (new \yii\db\Query())
    ->select(['T.id', 'I.name','T.price',
    'T.quantity','I.supplier','I.vat',
    'I.unit','T.ts_time'])
    ->from('items I')
    ->innerJoin('transactions T','T.item_id = I.id')
    ->where(
        [
            'T.invoice_id' => $id,
            'I.branch_id' => $branchId
        ]
        )
    ->all();
    
         $payPartial = Workflow::find()
         ->where(['branch_id' => $branchId,'workflow_code' => WorkflowConstant::PARTIAL_PAYMENT])
         ->count();   

        $station = User::findOne(Yii::$app->user->id)->branch;

        return $this->render('invoice', [
            'receipt_items' => $receipt_items,
            'receipt_details' => $receipt_details,
            'station' => $station,
            'payPartial' => $payPartial
        ]);
    }


    public function actionViewreceipt($id)
    {

        $connection = \Yii::$app->db;


        $model2 = $connection->createCommand("SELECT T.ts_time
        AS pdatetime,U.first_name,U.last_name,I.id as inv_id,
        C.fname,C.lname,C.phone,C.company,C.address,I.invoiceno
        FROM transactions T,customers C,user U, invoices I
        WHERE T.customer_id = C.id  
        AND T.user_id = U.id
        AND T.invoice_id = I.id
        AND I.status='PAID' 
        AND I.id = '".$id."' 
        GROUP BY T.ts_time,U.first_name,U.last_name,I.id,T.id,
        C.fname,C.lname,C.phone,C.company,C.address,I.invoiceno
        ORDER BY T.id DESC");
        $receipt_details = $model2->queryOne();


        $model = $connection->createCommand("SELECT T.id, I.name,T.price,T.quantity,I.supplier,I.vat,I.unit,T.ts_time 
        FROM items I,transactions T
        WHERE I.id = T.item_id
         AND T.invoice_id ='" .$id."'");
        $receipt_items = $model->queryAll();

        $query = (new \yii\db\Query())->from('transactions')->where(['invoice_id' => $id]);
        $modelsumt = $query->sum('price * quantity');

        $station = User::findOne(Yii::$app->user->id)->branch;

        return $this->render('//items/receipt_final', [
            'receipt_items' => $receipt_items,
            'receipt_details' => $receipt_details,
            'station' => $station,
            'modelsumt' => $modelsumt
        ]);
    }

    /**
     * Finds the Invoices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoices::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    
    /***api for mobile app****/
    
    public function actionList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $invoices = Invoices::find()
        ->select([
        'invoices.id',
        'invoices.invoiceno',
         "customers.lname AS customer_name",
        'invoices.created_at',
        'invoices.amount'
        ])
    ->leftJoin('customers', 'customers.id = invoices.customer_id')
    ->where(['invoices.branch_id' => $branchId])
    ->orderBy(['invoices.id' => SORT_DESC])
    ->asArray()
    ->all();
        return [
            'status' => 'success',
            'data' => $invoices,
        ];
    }
    
    public function actionItems()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $items = Items::find()
        ->alias('i')
        ->select([
            'i.id',
            'i.name',
            'price' => 'i.sprice',
            'quantity' => 'qt.quantity_t'
        ])
        ->leftJoin('quantity_total qt', 'qt.item_id = i.id')
        ->where(['i.branch_id' => $branchId])
        ->orderBy(['i.id' => SORT_DESC])
        ->asArray()
        ->all();
        
        return [
            'status' => 'success',
            'data' => $items,
        ];
    }
     
    public function actionCustomers()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $customers = Customers::find()
        ->select([
            'id',
            'name' => "CONCAT(fname, ' ', lname)",
            'phone'
        ])
        ->asArray()
        ->all();
        
        return [
            'status' => 'success',
            'data' => $customers,
        ];
    }
    
    public function actionSell()
    {
        
        Yii::$app->response->format = Response::FORMAT_JSON;
            
           $customer_id = Yii::$app->request->post('customer_id');
           $check_items = Yii::$app->request->post('items');
           
            Yii::info("Customer: $customer_id", "sell");
            Yii::info("Items: " . json_encode($check_items), "sell");
    
    
    
        
        $ts_time = new \yii\db\Expression('NOW()');
        $connection = \Yii::$app->db;

        foreach ($check_items as $ckeck) {
         
            $transaction = new Transactions();
            $transaction->status = "invoice";
            $transaction->ts_time = $ts_time;
            $transaction->customer_id = $customer_id;
            $transaction->user_id = Yii::$app->user->id;
            $transaction->trans_date = $ts_time;
            $transaction->price = $ckeck['price'];
            $transaction->item_id = $ckeck['id'];
            $transaction->payment_type = 1;
            $transaction->quantity = $ckeck['quantity'];
            $transaction->transaction_no = rand(100, 10000);
            $transaction->save(false);
            

            $connection->createCommand(
                "UPDATE quantity_total 
                    SET quantity_t = quantity_t - '" . $ckeck['quantity'] . "' 
                    WHERE  item_id = '" . $ckeck['id'] . "'"
            )->execute();
        }



        //create invoice number

        //Get station ID
        $stationId = User::findOne(Yii::$app->user->id)
            ->branch
            ->station_id;

        //Get branch ID
        $branchId = User::findOne(Yii::$app->user->id)
            ->branch_id;
        
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
        $inv->status = "PAID";
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
            )->execute();
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

      return [
            'status' => 'success',
            'data' => $inv->id,
        ];

       
    }
    
    public function actionDetails($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        
        $branch = Branch::findOne($branchId);
    
        $receiptDetails = (new \yii\db\Query())
            ->select([
                'DISTINCT(T.ts_time) AS pdatetime', 'T.id', 'U.first_name', 'U.last_name',
                'C.clogo', 'C.fname', 'C.lname', 'C.phone', 'C.company',
                'C.address'
            ])
            ->from('transactions T')
            ->innerJoin('customers C', 'T.customer_id = C.id')
            ->innerJoin('user U', 'T.user_id = U.id')
            ->where([
                'T.status' => 'invoice',
                'T.invoice_id' => $id
            ])
            ->orderBy('T.id DESC')
            ->one();
            
        $receiptDetails['branch']  = $branch;

        $receiptDetails['items'] = (new \yii\db\Query())
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
                    'T.status' => 'invoice',
                ]
            )
            ->all();
            
             return [
                'status' => 'success',
                'data' => $receiptDetails
            ];
            
    }
    
    public function actionDashboard()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
         
        $items = Items::find()->where(['branch_id' => $branchId])->count();
        $invoices = Invoices::find()->where(['branch_id' => $branchId])->count();
        $customers = Customers::find()->count();
    
       $dashboards['items'] = $items;
       $dashboards['invoices'] = $invoices;
       $dashboards['customers'] = $customers;
            
             return [
                'status' => 'success',
                'data' => $dashboards
            ];
            
    }
    
       public function actionReport()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $totalAmount = 0;
        
        $startDate = Yii::$app->request->post('start_date');
        $endDate   = Yii::$app->request->post('end_date');
        $reportType   = Yii::$app->request->post('report_type');
          
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;

    if($reportType == 'COLLECTION'){
        $totalAmount = Invoices::find()
        ->where(['branch_id' => $branchId])
        ->andWhere(['between', 'created_at', $startDate, $endDate])
        ->sum('amount');
    }
    
    if($reportType == 'PROFIT'){
        $totalSell = Invoices::find()
        ->where(['branch_id' => $branchId])
        ->andWhere(['between', 'created_at', $startDate, $endDate])
        ->sum('amount');
        
        $totalBuy = (new \yii\db\Query())
        ->select(['total2' => 'SUM(it.bprice * t.quantity)'])
        ->from(['it' => 'items'])
        ->innerJoin(['t' => 'transactions'], 'it.id = t.item_id')
        ->innerJoin(['inv' => 'invoices'], 'inv.id = t.invoice_id')
        ->where(['between', 'DATE(inv.created_at)', $startDate, $endDate])
        ->andWhere(['inv.status' => 'PAID'])
        ->andWhere(['inv.branch_id' => $branchId])
        ->scalar();
    
        $totalAmount = (float)$totalSell - (float)$totalBuy;
        
        }
    
             return [
                'status' => 'success',
                'result' => $totalAmount
            ];
            
    }
    
    
    public function actionAdditem()
    {
        
        Yii::$app->response->format = Response::FORMAT_JSON;
            
          $name = Yii::$app->request->post('name');
          $description = Yii::$app->request->post('description');
          $bprice = Yii::$app->request->post('bprice');
          $sprice = Yii::$app->request->post('sprice');
          $unit = Yii::$app->request->post('unit');
          $quantity = Yii::$app->request->post('quantity');
          $branchId = User::findOne(Yii::$app->user->id)->branch_id;
            
            $model = new Items();
             $model->name = $name;
             $model->description =$description;
             $model->bprice = $bprice;
             $model->sprice = $sprice;
             $model->vat = 18;
             $model->unit = $unit;
             $model->branch_id = $branchId;
             $model->uid =  Yii::$app->user->id;
             $model->save(false);
            
             $modelQ = new Quantity();
             
             $modelQ->q_date = date('Y-m-d');
             $modelQ->user_id= Yii::$app->user->id;
             $modelQ->quantity = $quantity;
             $modelQ->item_id = $model->id;
             $modelQ->save(false);
             
             
             $modelTotal = new QuantityTotal();
             
             $modelTotal->last_update = date('Y-m-d');
             $modelTotal->quantity_t = $quantity;
             $modelTotal->item_id = $model->id;
             $modelTotal->save(false);
            
        
        
            return [
                'status' => 'success',
                'data' => "Item saved successfully"
            ];
       
    }

     public function actionItemdetails($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $itemTotalq = QuantityTotal::find()->where(['item_id'=>$id])->one();
     
        $item = Items::findOne($id);
        $item->quantity == 0;
        
        if($itemTotalq){
         $item->quantity = $itemTotalq->quantity_t;
        }
       
         
             return [
                'status' => 'success',
              'data' => $item
            ];
            
    }
    
    public function actionAddquantity()
    {
        
        Yii::$app->response->format = Response::FORMAT_JSON;
            
           $item_id = Yii::$app->request->post('item_id');
           $quantity = Yii::$app->request->post('quantity');
          
            
             $modelQ = new Quantity();
             
             $modelQ->q_date = date('Y-m-d');
             $modelQ->user_id= Yii::$app->user->id;
             $modelQ->quantity = $quantity;
             $modelQ->item_id = $item_id;
             $modelQ->save(false);
             
             $check_found = QuantityTotal::find()
             ->where(['item_id'=>$item_id])
             ->one();
             
            if($check_found){
                $last_update = date('Y-m-d');
                $connection = \Yii::$app->db;
                $connection->createCommand(
                    "UPDATE quantity_total SET quantity_t = '".$quantity."' + '".$check_found->quantity_t."',last_update = '".$last_update."' WHERE item_id='".$item_id."'"
                    )
                    ->execute();
            }
            else{
                 $modelTotal = new QuantityTotal();
                 $modelTotal->last_update = date('Y-m-d');
                 $modelTotal->quantity_t = $quantity;
                 $modelTotal->item_id = $item_id;
                 $modelTotal->save(false);
            }
            
        
            return [
                'status' => 'success',
                'data' => "Item saved successfully"
            ];
       
    }


   public function actionUpdateitem()
    {
        
        Yii::$app->response->format = Response::FORMAT_JSON;
            
           $name = Yii::$app->request->post('name');
           $item_id = Yii::$app->request->post('item_id');
           $bprice = Yii::$app->request->post('bprice');
           $sprice = Yii::$app->request->post('sprice');
           $unit = Yii::$app->request->post('unit');
          
          
            
             $item = Items::findOne($item_id);
             
             $item->name = $name;
             $item->bprice = $bprice;
             $item->sprice = $sprice;
             $item->unit = $unit;
             $item->save(false);
            
        
        if($item){
            return [
                'status' => 'success',
                'data' => "Item saved successfully"
            ];
        }
        
          return [
                'status' => 'error',
                'data' => "Item not saved successfully"
            ];
       
    }
    

}
