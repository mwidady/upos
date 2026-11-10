<?php

namespace app\models;

use Yii;

class StationBillPayment extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_NOTIFIED = 'NOTIFIED';

    public static function tableName()
    {
        return 'station_bill_payments';
    }

    public function rules()
    {
        return [
            [['station_id', 'amount', 'reference_no', 'paid_by', 'paid_date'], 'required'],
            [['station_id', 'created_by'], 'integer'],
            [['amount'], 'number'],
            [['paid_date', 'created_at', 'notified_at'], 'safe'],
            [['notes'], 'string'],
            [['reference_no', 'paid_by'], 'string', 'max' => 150],
            [['notification_status'], 'string', 'max' => 20],
            [['station_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stations::className(), 'targetAttribute' => ['station_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'station_id' => 'Station',
            'amount' => 'Amount',
            'reference_no' => 'Reference No',
            'paid_by' => 'Paid By',
            'paid_date' => 'Paid Date',
            'notes' => 'Notes',
            'notification_status' => 'Notification Status',
            'notified_at' => 'Notified At',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    public static function notificationOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_NOTIFIED => 'Notified',
        ];
    }

    public function getStation()
    {
        return $this->hasOne(Stations::className(), ['id' => 'station_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}
