<?php

namespace Pitcher\models;

use Yii;
use yii\base\Model;

class UiForm extends Model
{
    // upload 
    public $uploadFile;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['uploadFile'], 'file', 'extensions' => 'mov, avi, mpg, ppt, pptx, key, mp4, m4v, pdf, mp3, wav, wma, zip'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'uploadFile' => Yii::t('app', 'File'),
        ];
    }
}
