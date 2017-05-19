<?php

namespace Pitcher\models;

use Yii;
use yii\base\ErrorException;
use yii\db\Query;

/**
 * This is the model class for table "{{%distributions}}".
 *
 * @property integer $id
 * @property string $distributionDate
 * @property string $expirationDate
 * @property string $startDate
 * @property integer $fileID
 * @property integer $coverNewDevices
 */
class Distribution extends \yii\db\ActiveRecord
{
    private $_devices;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%distributions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['distributionDate', 'expirationDate', 'startDate'], 'safe'],
            [['fileID'], 'required'],
            [['fileID', 'coverNewDevices'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'distributionDate' => 'Distribution Date',
            'expirationDate' => 'Expiration Date',
            'startDate' => 'Start Date',
            'fileID' => 'File ID',
            'coverNewDevices' => 'Cover New Devices',
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getDevices()
    {
        return Device::find()->leftJoin('map_device_app', '`map_device_app`.`device_id` = `tbl_devices`.`id`')
                             ->leftJoin('map_distribution_devices', '`map_distribution_devices`.`deviceID` = `map_device_app`.`id`')
                             ->Where(['map_distribution_devices.distributionID' => $this->id])
                             ->all();
    }

    public function linkDevices($devices)
    {
        if (!$this->id)
            return false;

        $db = Yii::$app->db;
        $session = Yii::$app->session;
        $app = PitApp::findOne($session["app-id"]);

        foreach ($devices as $device)
        {
            $db->createCommand("INSERT IGNORE INTO `map_device_app` (`device_id`, `app_id`) VALUES (:device_id, :app_id)")
               ->bindValue('device_id', $device->id)
               ->bindValue('app_id', $app->ID)
               ->execute();

            // last insert does not always work i.e. when keep distribution with multiple files is selected
            $mapDeviceApp = (new Query())->select('id')->from('map_device_app')->where(['device_id' => $device->id, 'app_id' => $app->ID])->one();

            $db->createCommand("INSERT IGNORE INTO `map_distribution_devices` (`deviceID`, `distributionID`) VALUES (:deviceID, :distributionID)")
               ->bindValue('deviceID', $mapDeviceApp["id"])
               ->bindValue('distributionID', $this->id)
               ->execute();
        }
    }

    public function unlinkDevices()
    {
        return Yii::$app->db
                        ->createCommand("DELETE FROM `map_distribution_devices` WHERE `map_distribution_devices`.`distributionID` = :distID")
                       ->bindValue(':distID', $this->id)
                       ->query();
    }
    
    public function getKeywords()
    {
        return (new Query())->select('id, keyword')
                     ->from('map_distribution_keyword')
                     ->Where(['map_distribution_keyword.distributionID' => $this->id])
                     ->all();
    }

    public function linkKeywords($keywords)
    {
        if (!$this->id)
            return false;

        $db = Yii::$app->db;

        foreach ($keywords as $keyword)
        {
            $db->createCommand("INSERT IGNORE INTO `map_distribution_keyword` (`distributionID`, `keyword`) VALUES (:distributionID, :keyword)")
               ->bindValue('distributionID', $this->id)
               ->bindValue('keyword', $keyword)
               ->execute();
        }
    }

    public function unlinkKeywords()
    {
        return Yii::$app->db
                        ->createCommand("DELETE FROM `map_distribution_keyword` WHERE `distributionID` = :distID")
                       ->bindValue(':distID', $this->id)
                       ->query();
    }
}
