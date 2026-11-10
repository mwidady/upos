<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stations".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $address
 * @property string $location
 * @property string $tel
 * @property string $fax
 * @property string $email
 * @property string $created_at
 * @property string $slogo
 * @property int $status
 *
 * @property Branch[] $branches
 */
class Stations extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'address', 'location', 'email'], 'required'],
            [['description'], 'string'],
            [['status'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 30],
            [['address', 'fax'], 'string', 'max' => 40],
            [['location'], 'string', 'max' => 20],
            [['tel'], 'string', 'max' => 50],
            [['slogo'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Station Name'),
            'description' => Yii::t('app', 'Description'),
            'address' => Yii::t('app', 'Address'),
            'location' => Yii::t('app', 'Location'),
            'tel' => Yii::t('app', 'Tel'),
            'fax' => Yii::t('app', 'Fax'),
            'email' => Yii::t('app', 'Email'),
            'created_at' => Yii::t('app', 'Created At'),
            'slogo' => Yii::t('app', 'Station logo'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public static function statusOptions()
    {
        return [
            self::STATUS_ENABLED => 'Enabled',
            self::STATUS_DISABLED => 'Disabled',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranches()
    {
        return $this->hasMany(Branch::className(), ['station_id' => 'id']);
    }
}
