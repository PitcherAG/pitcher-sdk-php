<?php

namespace Pitcher\models;

use Yii;
use yii\web\HttpException;

/**
 * This is the model class for table "map_distribution_keyword".
 *
 * @property integer $id
 * @property integer $distributionID
 * @property string $keyword
 */
class DistributionKeyword extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_distribution_keyword';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['distributionID'], 'required'],
            [['distributionID'], 'integer'],
            [['keyword'], 'string', 'max' => 255],
            [['distributionID', 'keyword'], 'unique', 'targetAttribute' => ['distributionID', 'keyword'], 'message' => 'The combination of Distribution ID and Keyword has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'distributionID' => 'Distribution ID',
            'keyword' => 'Keyword',
        ];
    }

    public static function findByKeyword($keyword)
    {
        $appId = Yii::$app->session["app-id"];

        if (!$appId) throw new \InvalidArgumentException("The requested argument appID is missing.");

        $res = Yii::$app->db->createCommand("SELECT map_distribution_keyword.id FROM map_distribution_keyword
                                             INNER JOIN tbl_distributions
                                             ON map_distribution_keyword.distributionID = tbl_distributions.id
                                             INNER JOIN tbl_files
                                             ON tbl_distributions.fileID = tbl_files.ID
                                             INNER JOIN tbl_apps
                                             ON tbl_files.appID = tbl_apps.ID
                                             INNER JOIN map_users_apps
                                             ON tbl_apps.ID = map_users_apps.appID
                                             WHERE tbl_apps.ID = :appID
                                             AND map_distribution_keyword.keyword = :keyword
                                             AND map_users_apps.userID = :userID")
            ->bindValue('appID', $appId)
            ->bindValue('keyword', $keyword)
            ->bindValue('userID', Yii::$app->user->id)
            ->queryAll();

        return self::findAll(array_map(function($a) { return $a["id"]; }, $res));
    }
}
