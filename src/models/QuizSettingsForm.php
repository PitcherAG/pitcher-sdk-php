<?php

namespace Pitcher\models;

use Yii;
use yii\base\Model;

class QuizSettingsForm extends Model
{
    public $type;
    public $questionPerPage;
    public $transitionType;
    public $containerWidth;
    public $containerHeight;
    public $margin;
    public $bgColor;
    public $bgImage;
    public $hasOpeningPage;
    public $scoringType;
    public $pointPerQuestion;
    public $translatable;
    public $passScore;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['type', 'transitionType','bgColor','scoringType'], 'string'],
            [['margin','pointPerQuestion', 'containerWidth','containerHeight','passScore'], 'integer'],
            [['containerWidth','containerHeight'], 'integer', 'min'=>300],
            [['margin'], 'integer', 'max'=>250],
            [['passScore'], 'integer', 'min'=>0, 'max'=>100],
            [['questionPerPage', 'hasOpeningPage'], 'boolean'],
            [['bgImage'], 'file', 'extensions' => 'png,jpg,jpeg'],
            [['type', 'questionPerPage'], 'required'],
            [['bgImage'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'type'=> 'Content Type',
            'questionPerPage'=> 'Single Question Per Page',
            'translatable'=> 'Translatable',
            'transitionType'=> 'Transition Type',
            'containerWidth'=> 'Container Width',
            'containerHeight'=> 'Container Height',
            'margin'=> 'Margin',
            'bgColor'=> 'Background Color',
            'bgImage'=> 'Background Image',
            'hasOpeningPage'=> 'Opening Page',
            'scoringType'=> 'Scoring Type',
            'pointPerQuestion'=> 'Point Per Question',
            'passScore'=> 'Passing Score'
        ];
    }
}
