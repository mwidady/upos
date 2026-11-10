<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customers".
 *
 * @property int $id
 * @property string $fname
 * @property string $lname
 * @property string $gender
 * @property string $phone
 * @property string $company
 * @property string $clogo
 * @property string $position
 * @property string $customer_no
 * @property string $address
 * @property string $email
 * @property int $branch_id
 *
 * @property Branch $branch
 * @property Invoices[] $invoices
 * @property Transactions[] $transactions
 */
class Customers extends \yii\db\ActiveRecord
{
    
    public $payment_option;

    public function hasUnpaidInvoices()
    {
        return $this->getInvoices()
            ->andWhere(['status' => ['NOT PAID', 'PAID PARTIAL']])
            ->exists();
    }

    public function getDisplayName()
    {
        return trim($this->fname . ' ' . $this->lname);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fname', 'lname',  'phone',  'customer_no','company'], 'required'],
            [['branch_id'], 'integer'],
            [['fname', 'lname', 'position', 'customer_no', 'address', 'email'], 'string', 'max' => 30],
            [['gender'], 'string', 'max' => 10],
            [['phone'], 'string', 'max' => 20],
            [['company'], 'string', 'max' => 50],
            [['clogo'], 'string', 'max' => 40],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fname' => 'Fname',
            'lname' => 'Lname',
            'gender' => 'Gender',
            'phone' => 'Phone',
            'company' => 'Company',
            'clogo' => 'Clogo',
            'position' => 'Position',
            'customer_no' => 'Customer No',
            'address' => 'Address',
            'email' => 'Email',
            'branch_id' => 'Branch ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoices::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transactions::className(), ['customer_id' => 'id']);
    }
}
