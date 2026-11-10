<?php

namespace app\models;

use yii\base\Model;

class UserPasswordForm extends Model
{
    public $new_password;
    public $repeat_password;

    public function rules()
    {
        return [
            [['new_password', 'repeat_password'], 'required'],
            [['new_password', 'repeat_password'], 'string', 'min' => 5, 'max' => 250],
            ['repeat_password', 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'new_password' => 'New Password',
            'repeat_password' => 'Repeat Password',
        ];
    }
}
