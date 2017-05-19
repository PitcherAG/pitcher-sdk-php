<?php

namespace Pitcher\models;

use Yii;

class Questions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['testIDv2', 'question'], 'required'],
            [['question', 'option1', 'option2', 'option3', 'option4', 'option5', 'option6', 'option7', 'option8', 'option9', 'option10'], 'string'],
            [['testIDv2', 'questionType'], 'integer'],
            [['testID','correctanswer','option1', 'option2', 'option3', 'option4', 'option5', 'option6', 'option7', 'option8', 'option9', 'option10'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'testIDv2' => 'Test ID',
            'question' => 'Question',
            'correctanswer'=>'Correct Answer',
            'questionType'=>'Question Type',
            'option1'=>'Option1',
            'option2'=>'Option2',
            'option3'=>'Option3',
            'option4'=>'Option4',
            'option5'=>'Option5',
            'option6'=>'Option6',
            'option7'=>'Option7',
            'option8'=>'Option8',
            'option9'=>'Option9',
            'option10'=>'Option10'
        ];
    }

    public function getQuizSurveySettings()
    {
        return $this->hasOne(QuizSurveySettings::className(), ['ID' => 'testIDv2']);
    }

}
