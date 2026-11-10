<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property int $item_id
 * @property double $price
 * @property int $quantity
 * @property int $user_id
 * @property string $trans_date
 * @property int $customer_id
 * @property string $payment_type
 * @property string $transaction_no
 * @property string $status
 * @property string $ts_time
 * @property int $invoice_id
 *
 * @property Customers $customer
 * @property Invoices $invoice
 * @property Items $item
 * @property User $user
 */
class Transactions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'price', 'quantity', 'user_id', 'trans_date', 'customer_id', 'payment_type', 'transaction_no'], 'required'],
            [['item_id', 'quantity', 'user_id', 'customer_id', 'invoice_id'], 'integer'],
            [['price'], 'number'],
            [['trans_date', 'ts_time'], 'safe'],
            [['payment_type', 'status'], 'string', 'max' => 20],
            [['transaction_no'], 'string', 'max' => 50],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoices::className(), 'targetAttribute' => ['invoice_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Items::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'item_id' => Yii::t('app', 'Item ID'),
            'price' => Yii::t('app', 'Price'),
            'quantity' => Yii::t('app', 'Quantity'),
            'user_id' => Yii::t('app', 'User ID'),
            'trans_date' => Yii::t('app', 'Trans Date'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'payment_type' => Yii::t('app', 'Payment Type'),
            'transaction_no' => Yii::t('app', 'Transaction No'),
            'status' => Yii::t('app', 'Status'),
            'ts_time' => Yii::t('app', 'Ts Time'),
            'invoice_id' => Yii::t('app', 'Invoice ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::className(), ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
