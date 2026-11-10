<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "expenditures".
 *
 * @property int $id
 * @property string $ename
 * @property string $etype
 * @property int $expense_type_id
 * @property double $amount
 * @property string $company
 * @property string $phone
 * @property int $user_id
 * @property string $edate
 * @property int $branch_id
 *
 * @property Branch $branch
 * @property User $user
 */
class Expenditures extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expenditures';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ename', 'etype', 'expense_type_id', 'amount', 'user_id'], 'required'],
            [['amount'], 'number'],
            [['user_id', 'branch_id', 'expense_type_id'], 'integer'],
            [['edate'], 'safe'],
            [['ename'], 'string', 'max' => 255],
            [['etype'], 'string', 'max' => 60],
            [['company'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 12],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['expense_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExpenseType::className(), 'targetAttribute' => ['expense_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ename' => 'Expense Name',
            'etype' => 'Expense Type',
            'expense_type_id' => 'Expense Type',
            'amount' => 'Amount',
            'company' => 'Company',
            'phone' => 'Phone',
            'user_id' => 'User ID',
            'edate' => 'Date Applied',
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getExpenseType()
    {
        return $this->hasOne(ExpenseType::className(), ['id' => 'expense_type_id']);
    }
}

