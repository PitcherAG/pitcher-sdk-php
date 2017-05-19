<?php

namespace Pitcher\models;

use Yii;

class QuizSurveySettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_quiz_survey_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['general_settings', 'questions','appID','original_name'], 'required'],
            [['general_settings', 'questions', 'original_name'], 'string'],
            [['status', 'appID', 'uploaded_by','fileID'], 'integer'],
            [['edit_time', 'creation_time','publish_time','fileID','uploaded_by','status'], 'safe'],
            [['original_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'general_settings' => 'General Settings',
            'questions' => 'Questions',
            'status' => 'Published?',
            'appID' => 'App ID',
            'edit_time' => 'Edit Time',
            'publish_time' => 'Published On',
            'creation_time' => 'Created On',
            'fileID' => 'File ID',
            'uploaded_by' => 'Uploaded By',
            'original_name' => 'Original Name'
        ];
    }

    public function getUploadedBy()
    {
        return $this->hasOne(User::className(), ['ID' => 'uploaded_by']);
    }

    public function getFile()
    {
        return $this->hasOne(File::className(), ['ID' => 'fileID']);
    }

    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), ['testIDv2' => 'ID']);
    }

}
