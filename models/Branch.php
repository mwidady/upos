<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "branch".
 *
 * @property int $id
 * @property int $station_id
 * @property int $created_by
 * @property string $name
 * @property string $description
 * @property string $address
 * @property string $location
 * @property string $tel
 * @property string $fax
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Stations $station
 * @property User $createdBy
 */
class Branch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'branch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['station_id', 'created_by', 'name', 'description', 'address', 'location', 'email'], 'required'],
            [['station_id', 'created_by'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 30],
            [['address', 'fax'], 'string', 'max' => 40],
            [['location'], 'string', 'max' => 20],
            [['tel'], 'string', 'max' => 50],
            [['station_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stations::className(), 'targetAttribute' => ['station_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'station_id' => Yii::t('app', 'Station Name'),
            'created_by' => Yii::t('app', 'Created By'),
            'name' => Yii::t('app', 'Branch Name'),
            'description' => Yii::t('app', 'Description'),
            'address' => Yii::t('app', 'Address'),
            'location' => Yii::t('app', 'Location'),
            'tel' => Yii::t('app', 'Tel'),
            'fax' => Yii::t('app', 'Fax'),
            'email' => Yii::t('app', 'Email'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStation()
    {
        return $this->hasOne(Stations::className(), ['id' => 'station_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}
