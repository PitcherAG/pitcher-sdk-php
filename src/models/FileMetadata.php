<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "{{%file_metadata}}".
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 * @property integer $fileID
 */
class FileMetadata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_metadata}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['fileID'], 'integer'],
            [['key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
            'fileID' => 'File ID',
        ];
    }
}
