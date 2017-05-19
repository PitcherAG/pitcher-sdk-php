<?php

namespace Pitcher\models;

use Yii;
use yii\base\Model;

class QuizForm extends Model
{
    // file
    public $title;
    public $keywords;
    public $navigation;
    public $subNavigation;

    // distribution
    public $distOffset;
    public $distFrom;
    public $distTo;
    public $distDevices;
    public $distGroups;
    public $distAll;

    // notifications
    public $notificationEnabled;
    public $notificationTxt;

    /**
     * @return array the validation rules.
     */

    public function rules()
    {
        return [
            [['title', 'keywords', 'notificationTxt'], 'string'],
            [['navigation', 'subNavigation','distOffset'], 'integer'],
            [['distAll', 'notificationEnabled'], 'boolean'],
            [['distFrom', 'distTo'], 'date', 'format' => 'M/d/yyyy h:mm a'],
            [['distDevices', 'distGroups'], 'safe'],
            [['title', 'distFrom', 'distTo'], 'required'],
            ['notificationTxt', 'required', 'when' => function($model) {
                return $model->notificationEnabled == 1;
            }, 'whenClient' => "function (attribute, value) {
                return $('#fileform-notificationenabled')[0].checked;
            }"],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'keywords' => 'Keywords(,)',
            'navigation' => 'Navigation',
            'subNavigation' => 'Sub Navigation',
            'distFrom' => 'Starting from',
            'distTo' => 'Until',
            'distDevices' => 'Devices',
            'distGroups' => 'Groups',
            'distAll' => ' Distribute to all (including new) devices and groups',
            'notificationEnabled' => 'Send push notification to users on completion',
            'notificationTxt' => 'Push notification message',
        ];
    }
}
