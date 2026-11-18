<?php

namespace app\models;

use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $current_password;
    public $new_password;
    public $repeat_password;

    private $user;

    public function __construct(User $user, $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['current_password', 'new_password', 'repeat_password'], 'required'],
            [['current_password', 'new_password', 'repeat_password'], 'string', 'min' => 5, 'max' => 250],
            ['current_password', 'validateCurrentPassword'],
            ['repeat_password', 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'current_password' => 'Current Password',
            'new_password' => 'New Password',
            'repeat_password' => 'Repeat New Password',
        ];
    }

    public function validateCurrentPassword($attribute)
    {
        if (!$this->hasErrors() && !$this->user->validatePassword($this->$attribute)) {
            $this->addError($attribute, 'Current password is incorrect.');
        }
    }
}
