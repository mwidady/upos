<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "items".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property double $bprice
 * @property double $sprice
 * @property string $vat
 * @property int $uid
 * @property string $unit
 * @property string $supplier
 * @property string $sphone
 * @property int $branch_id
 *
 * @property Branch $branch
 * @property User $u
 * @property Quantity[] $quantities
 * @property QuantityTotal[] $quantityTotals
 * @property Transactions[] $transactions
 */
class Items extends \yii\db\ActiveRecord
{
    public $quantity;
    public $excelFile;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'items';
    }
    
    public function fields()
{
    $fields = parent::fields();
    $fields[] = 'quantity'; // allow quantity in API output
    return $fields;
}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'bprice', 'sprice', 'uid', 'unit'], 'required'],
            [['description'], 'string'],
            [['brand'], 'string'],
            [['excelFile'], 'file', 'skipOnEmpty' => true, 'extensions' => ['xls', 'xlsx', 'csv']],
            [['bprice', 'sprice'], 'number'],
            [['uid', 'branch_id'], 'integer'],
            [['name', 'supplier', 'sphone'], 'string', 'max' => 255],
            [['vat'], 'string', 'max' => 3],
            [['unit'], 'string', 'max' => 40],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'brand' => 'Brand',
            'bprice' => 'Bprice',
            'sprice' => 'Sprice',
            'vat' => 'Vat',
            'uid' => 'Uid',
            'unit' => 'Unit',
            'supplier' => 'Supplier',
            'sphone' => 'Sphone',
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
    public function getU()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuantities()
    {
        return $this->hasMany(Quantity::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuantitytotal()
    {
        return $this->hasMany(QuantityTotal::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transactions::className(), ['item_id' => 'id']);
    }
}

