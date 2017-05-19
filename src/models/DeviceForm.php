<?php

namespace Pitcher\models;

use yii;
use yii\base\Model;

class DeviceForm extends Model
{
    const SCENARIO_UDID = 'udid';
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_CODE = 'code';

    public $udid;
    public $name;
    public $email;
    public $keywords;
    public $username;
    public $password;
    public $validity;
    public $isUser;
    public $isAdmin;

    public function scenarios()
    {
        return [
            self::SCENARIO_UDID => ['udid', 'name', 'isUser', 'email', 'keywords', 'isAdmin'],
            self::SCENARIO_LOGIN => ['username', 'password', 'isUser', 'email'],
            self::SCENARIO_CODE => ['validity', 'keywords'],
        ];
    }

    public function rules()
	{
	    return [
	        [['udid', 'name'], 'required', 'on' => self::SCENARIO_UDID],
	        [['username', 'password'], 'required', 'on' => self::SCENARIO_LOGIN],
	        [['validity'], 'required', 'on' => self::SCENARIO_CODE],

            [['udid'], 'string', 'min' => 40, 'max' => 255],
            [['name'], 'string', 'max' => 255],
	        [['keywords', 'username', 'password'], 'string'],
	        [['isUser', 'isAdmin'], 'safe'],
        	['email', 'email'],
            ['email', 'required', 'when' => function($model) { return $model->isUser == 1; }, 'whenClient' => "function (attribute, value) { return false; }"],
        	['validity', 'date'],

	        [['udid', 'name', 'keywords', 'username', 'password'], 'trim'],

	    ];
	}

    public function attributeLabels()
    {
        return [
            'udid' => Yii::t('app', 'Device ID'),
            'name' => Yii::t('app', 'Device Name'),
            'isUser' => Yii::t('app', 'Create admin user'),
            'isAdmin' => Yii::t('app', 'Mark as test device'),
            'body' => Yii::t('app', 'Content'),
            'keywords' => Yii::t('app', 'Keywords(,)'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
        ];
    }
}