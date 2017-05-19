<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "map_users_apps".
 *
 * @property integer $ID
 * @property integer $appID
 * @property integer $userID
 */
class MapUserApp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_users_apps';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appID', 'userID'], 'required'],
            [['appID', 'userID'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'appID' => 'App ID',
            'userID' => 'User ID',
        ];
    }
}
