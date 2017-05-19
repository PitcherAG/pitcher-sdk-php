<?php

namespace Pitcher\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "map_app_uniquecode".
 *
 * @property integer $id
 * @property string $uniquecode
 * @property string $appIDs
 * @property string $expiryDate
 * @property string $metadata
 * @property PitApp $app
 */
class Licensee extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_licensees';
    }

    public function getApp()
    {
        $this->hasOne(PitApp::className(), ['licenseID' => 'id']);
    }
}
