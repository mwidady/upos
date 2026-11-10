<?php

namespace app\models;

use yii\helpers\ArrayHelper;

class ExpenseType extends \yii\db\ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    public static function tableName()
    {
        return 'expense_types';
    }

    public function rules()
    {
        return [
            [['branch_id', 'name'], 'required'],
            [['branch_id', 'status', 'created_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 60],
            [['name'], 'trim'],
            [['name'], 'unique', 'targetAttribute' => ['branch_id', 'name'], 'message' => 'This expense type already exists for your branch.'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Expense Type',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function statusOptions()
    {
        return [self::STATUS_ENABLED => 'Enabled', self::STATUS_DISABLED => 'Disabled'];
    }

    public static function optionsForBranch($branchId, $includeId = null)
    {
        $query = static::find()->where(['branch_id' => $branchId]);
        if ($includeId) {
            $query->andWhere(['or', ['status' => self::STATUS_ENABLED], ['id' => $includeId]]);
        } else {
            $query->andWhere(['status' => self::STATUS_ENABLED]);
        }

        return ArrayHelper::map($query->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}

