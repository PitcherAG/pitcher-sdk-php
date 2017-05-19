<?php

namespace Pitcher\models;

use Yii;

class IndoorsSharedFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_indoors_shared_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fileID','created_by'], 'required'],
            [['alias'], 'string'],
            [['fileID', 'created_by'], 'integer'],
            [['created_at', 'status_updated_at','is_active','status_updated_by'], 'safe'],
            [['alias'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_active' => 'is_active',
            'fileID' => 'File ID',
            'alias' => 'alias',
            'created_at' => 'created_at',
            'created_by' => 'created_by',
            'status_updated_by' => 'status_updated_by'
        ];
    }

    public function getFile()
    {
        return $this->hasOne(File::className(), ['ID' => 'fileID']);
    }

   /**
   * @inheritdoc
   */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
				if(empty($this->alias)){
					$this->alias = $this->getUniqueRandomString();
				}
            return true;
        } else {
            return false;
        }
    }

   private function getUniqueRandomString() {
   	$randomStr =  $this->generateRandomString();
  		$sharedFile = $this->find()->where(['alias'=>$randomStr])->one();
  		if($sharedFile) {
  				return $this->getUniqueRandomString();
  		} else {
  			return $randomStr;
  		}
   }

    private function generateRandomString($length = 8) {
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$charactersLength = strlen($characters);
    	$randomString = '';
    	for ($i = 0; $i < $length; $i++) {
    		$randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    	}
    	return $randomString;
    }

}
