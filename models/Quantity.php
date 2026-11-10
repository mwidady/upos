<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quantity".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $user_id
 * @property string $q_date
 * @property integer $quantity
 *
 * @property User $user
 * @property Items $item
 */
class Quantity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quantity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'user_id', 'q_date', 'quantity'], 'required'],
            [['item_id', 'user_id', 'quantity'], 'integer'],
            [['q_date'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Items::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item Name',
            'user_id' => 'Username',
            'q_date' => 'Date Reg',
            'quantity' => 'Quantity',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }
}
