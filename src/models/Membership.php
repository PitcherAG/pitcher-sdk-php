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
class Membership extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_memberships';
    }
}
