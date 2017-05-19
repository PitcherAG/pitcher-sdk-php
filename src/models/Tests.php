<?php

namespace Pitcher\models;

use Yii;

class Tests extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','appID'], 'required'],
            [['name'], 'string'],
            [['testType', 'appID', 'isPublished','createdBy','publishedBy','templateID','fileID'], 'integer'],
            [['lastEditTime', 'publicationTime','creationTime','templateID','publishedBy','createdBy'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'name' => 'Name',
            'isPublished' => 'Published',
            'creationTime' => 'Creation Time',
            'publicationTime' => 'Publication Time',
            'createdBy' => 'Created By',
            'templateID' => 'Template ID',
            'fileID' => 'File ID',
            'publishedBy' => 'Published By'
        ];
    }
}
