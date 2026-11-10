<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quantity_total".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $quantity_t
 * @property string $last_update
 */
class QuantityTotal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quantity_total';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'quantity_t', 'last_update'], 'required'],
            [['item_id', 'quantity_t'], 'integer'],
            [['last_update'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item ID',
            'quantity_t' => 'Quantity T',
            'last_update' => 'Last Update',
        ];
    }

}


