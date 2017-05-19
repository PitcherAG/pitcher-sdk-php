<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "map_groups_files".
 *
 * @property integer $ID
 * @property integer $groupID
 * @property integer $fileID
 * @property integer $subContentID
 * @property integer $oneWay
 * @property integer $orderOnList
 * @property File $file
 */
class MapGroupFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_groups_files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupID', 'fileID', 'subContentID', 'oneWay', 'orderOnList'], 'integer'],
            [['orderOnList'], 'required'],
            [['groupID', 'fileID', 'subContentID'], 'unique', 'targetAttribute' => ['groupID', 'fileID', 'subContentID'], 'message' => 'The combination of Group ID, File ID and Sub Content ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'groupID' => 'Group ID',
            'fileID' => 'File ID',
            'subContentID' => 'Sub Content ID',
            'oneWay' => 'One Way',
            'orderOnList' => 'Order On List',
        ];
    }

    public function getFile()
    {
        return $this->hasOne(File::className(), ['ID' => 'fileID']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['ID' => 'groupID']);
    }
}
