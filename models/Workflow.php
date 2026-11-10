<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "workflow".
 *
 * @property int $id
 * @property int $branch_id
 * @property string $workflow_code
 *
 * @property Branch $branch
 */
class Workflow extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'workflow';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'workflow_code'], 'required'],
            [['branch_id'], 'integer'],
            [['workflow_code'], 'string', 'max' => 25],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::class, 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'workflow_code' => 'Workflow Code',
        ];
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::class, ['id' => 'branch_id']);
    }
}
