<?php

namespace Pitcher\models;

use yii\db\ActiveRecord;

class Sse extends ActiveRecord
{   
    public static function tableName()
    {
        return '{{%apps}}';
    }

    public function index(){
    	
    }
}