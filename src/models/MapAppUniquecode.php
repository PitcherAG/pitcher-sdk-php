<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "map_app_uniquecode".
 *
 * @property integer $id
 * @property string $uniquecode
 * @property string $appIDs
 * @property string $expiryDate
 * @property string $metadata
 */
class MapAppUniquecode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_app_uniquecode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expiryDate'], 'required'],
            [['expiryDate'], 'safe'],
            [['uniquecode'], 'string', 'max' => 8],
            [['appIDs', 'metadata'], 'string', 'max' => 255],
            [['uniquecode'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uniquecode' => Yii::t('app', 'Uniquecode'),
            'appIDs' => Yii::t('app', 'App Ids'),
            'expiryDate' => Yii::t('app', 'Expiry Date'),
            'metadata' => Yii::t('app', 'Keywords(,)'),
        ];
    }
}
