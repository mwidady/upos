<?php

namespace app\models;
use Yii;

use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    public $old_password;
    public $new_password;
    public $repeat_password;

    //public $username;

    public static function tableName() { return 'user'; }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','password','branch_id','station_id'], 'required'],
            [['branch_id','station_id', 'status'], 'integer'],
            [['branch_id'], 'safe'],
            [['email'], 'email'],
            [['username','email'], 'unique'],
            [['phone_number'],'string','max' => 30],
            [['username','password','password_reset_token','first_name','last_name'], 'string', 'max' => 250],
            [['password','new_password','repeat_password'],'required'],
            ['password','findPasswords'],
            ['repeat_password','compare','compareAttribute'=>'new_password'],
            [['user_image','email'], 'string', 'max' => 500],
            [['userimage'], 'file'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'branch_id' => 'Branch',
            'station_id' => 'Station',
            'username' => 'Username',
            'station_id' => 'Station Name',
            'status' => 'Status',
        ];
    }

    public static function statusOptions()
    {
        return [
            self::STATUS_ENABLED => 'Enabled',
            self::STATUS_DISABLED => 'Disabled',
        ];
    }


    public static function findIdentity($id) {
        $user = self::find()
            ->where([
                "id" => $id
            ])
            ->one();
        if($user == null || !$user->canLogin()){
            return null;
        }
        return new static($user);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $userType = null) {

        $user = self::find()
            ->where(["accessToken" => $token])
            ->one();
        // if (!count($user)) {
        //     return null;
        // }
        if($user == null){
            return null;
        }
        return new static($user);
    }
    //matching the old password with your existing password.
    public function findPasswords($attribute, $params)
    {
        $user = User::model()->findByPk(Yii::app()->user->id);
        if ($user->password != md5($this->password))
            $this->addError($attribute, 'Old password is incorrect.');
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username) {
        $user = self::find()
            ->where([
                "username" => $username
            ])
            ->one();
        if($user == null){
            return null;
        }
        return new static($user);
    }
    public function getUsername()
    {
        return \Yii::$app->user->identity->username;
    }

    public function getFname()
    {
        return \Yii::$app->user->identity->fname;
    }
    public static function findByUser($username) {
        $user = self::find()
            ->where([
                "username" => $username
            ])
            ->one();
        if (!count($user)) {
            return null;
        }
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->authKey;
    }

    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return $this->password ===  md5($password);
    }

    public function getStation()
    {
        return $this->hasOne(Stations::className(), ['id' => 'station_id']);
    }

    public function canLogin()
    {
        if ((int) $this->status !== self::STATUS_ENABLED) {
            return false;
        }

        if ($this->branch && $this->branch->station && (int) $this->branch->station->status !== Stations::STATUS_ENABLED) {
            return false;
        }

        return true;
    }

    public function getLoginBlockReason()
    {
        if ((int) $this->status !== self::STATUS_ENABLED) {
            return 'Your user account is disabled. Please contact the administrator.';
        }

        if ($this->branch && $this->branch->station && (int) $this->branch->station->status !== Stations::STATUS_ENABLED) {
            return 'Your station is disabled. Login is currently restricted for all users under this station.';
        }

        return null;
    }

    /**
     * Get relationship between user and branch
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }
}
