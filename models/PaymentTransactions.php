<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment_transactions".
 *
 * @property int $id
 * @property double $amount_paid
 * @property double $amount_remain
 * @property double $initial_amount
 * @property string $paid_through
 * @property string $account_no
 * @property string $paid_by
 * @property int $received_by
 * @property int $invoice_id
 * @property string $paid_date
 * @property int $created_by
 * @property string $created_at
 *
 * @property Invoices $invoice
 * @property User $receivedBy
 */
class PaymentTransactions extends \yii\db\ActiveRecord
{
    public $status;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['amount_paid', 'amount_remain', 'initial_amount'], 'number'],
            [['received_by', 'invoice_id', 'created_by'], 'integer'],
            [['paid_date', 'created_at', 'status'], 'safe'],
            [['paid_through', 'account_no', 'paid_by'], 'string', 'max' => 255],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoices::className(), 'targetAttribute' => ['invoice_id' => 'id']],
            [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['received_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount_paid' => 'Amount Paid',
            'amount_remain' => 'Amount Remain',
            'initial_amount' => 'Initial Amount',
            'paid_through' => 'Paid Through',
            'account_no' => 'Account No',
            'paid_by' => 'Paid By',
            'received_by' => 'Received By',
            'invoice_id' => 'Invoice ID',
            'paid_date' => 'Paid Date',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
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
    public function getReceivedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'received_by']);
    }
}
