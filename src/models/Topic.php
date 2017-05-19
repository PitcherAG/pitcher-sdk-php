<?php

namespace Pitcher\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%topics}}".
 *
 * @property integer $id
 * @property string $topic
 * @property string $external_ID
 * @property integer $appID
 * @property string $objectType
 * @property string $orgID
 * @property string $syncDate
 */
class Topic extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%topics}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appID'], 'integer'],
            [['syncDate'], 'safe'],
            [['topic', 'external_ID', 'objectType', 'orgID'], 'string', 'max' => 255],
            [['topic', 'appID', 'orgID'], 'unique', 'targetAttribute' => ['topic', 'appID', 'orgID'], 'message' => 'The combination of Topic, App ID and Org ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'topic' => 'Topic',
            'external_ID' => 'External  ID',
            'appID' => 'App ID',
            'objectType' => 'Object Type',
            'orgID' => 'Org ID',
            'syncDate' => 'Sync Date',
        ];
    }
}
