<?php

namespace Pitcher\models;

use Yii;
use app\models\PitApp;
use app\models\User;
use app\models\Licensee;
use app\models\Membership;

/**
 * This is the model class for table "{{%devices}}".
 *
 * @property integer $id
 * @property string $udid
 * @property string $deviceName
 * @property string $creationTime
 * @property string $metadata
 * @property string $appVersion
 * @property string $lastUpdate
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%devices}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['udid', 'deviceName'], 'required'],

            [['creationTime', 'lastUpdate'], 'safe'],
            [['metadata'], 'string'],
            [['deviceName', 'appVersion'], 'string', 'max' => 255],
            [['udid'], 'unique'],
            ['udid', 'string',  'min' => 36, 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'udid' => Yii::t('app', 'Device ID'),
            'deviceName' => Yii::t('app', 'Device Name'),
            'creationTime' => 'Creation Time',
            'metadata' => 'Metadata',
            'appVersion' => 'App Version',
            'lastUpdate' => 'Last Update',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) 
        {
            if ($this->isNewRecord) 
            {
                $this->creationTime = date("Y-m-d H:i:s");
				//get app
				$session = Yii::$app->session;
				$app = PitApp::findOne(['ID' => $session['app-id']]);
				$licensee=Licensee::findOne(['ID' => $app->licenseID]);
				if($licensee->memberType){
					$membership = Membership::findOne(["id"=>$licensee->memberType]);
					$mapDeviceApp = MapDeviceApp::findAll(["app_id"=>$app->ID]);
					$existingCount = count($mapDeviceApp);
					
					if($existingCount<$membership->maxUsers){
						return true;
					}
					else{
						return false;
					}
					
				}
				
            }
            else
            {
                $this->lastUpdate = date("Y-m-d H:i:s");                
            }
			
			
            return true;
        }

        return false;
    }

    public function getMapApps()
    {
        return $this->hasMany(MapDeviceApp::className(), ['device_id' => 'id']);
    }

    public function getApps()
    {
        return $this->hasMany(Item::className(), ['id' => 'app_id'])
            ->via('mapApps');
    }

    public function getIPadUser()
    {
        return $this->hasOne(IPadUser::className(), ['username' => 'udid']);
    }

    public function getFiles()
    {
        return File::find()->leftJoin('tbl_distributions', '`tbl_distributions`.`fileID` = `tbl_files`.`ID`')
                           ->leftJoin('map_distribution_devices', '`map_distribution_devices`.`distributionID` = `tbl_distributions`.`id`')
                           ->leftJoin('map_device_app', '`map_device_app`.`id` = `map_distribution_devices`.`deviceID`')
                           ->where(['map_device_app.device_id' => $this->id]);
    }
}
