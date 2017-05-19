<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "map_device_app".
 *
 * @property integer $id
 * @property integer $device_id
 * @property integer $app_id
 * @property integer $metadata
 * @property integer $isTest
 */
class MapDeviceApp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_device_app';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_id', 'app_id'], 'required'],
            [['device_id', 'app_id', 'metadata'], 'integer'],
            [['isTest'], 'safe'],
            [['device_id', 'app_id'], 'unique', 'targetAttribute' => ['device_id', 'app_id'], 'message' => 'The combination of Device ID and App ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_id' => 'Device ID',
            'app_id' => 'App ID',
            'metadata' => 'Metadata',
            'isTest' => Yii::t('app', 'Mark as test device'),
        ];
    }
}
