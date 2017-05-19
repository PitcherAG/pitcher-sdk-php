<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "{{%groups}}".
 *
 * @property integer $ID
 * @property string $group_name
 * @property integer $appID
 * @property integer $isPDFOnly
 * @property integer $locked
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%groups}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appID', 'isPDFOnly','locked'], 'integer'],
            [['group_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'group_name' => 'Group Name',
            'appID' => 'App ID',
            'isPDFOnly' => 'Is Pdfonly',
            'locked' => 'Locked'
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {

            // delete related records
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $fileMaps  = $this->getFileMaps()->all();

            try {
                foreach ($fileMaps as $fileMap) {
                    $fileMap->delete();
                }

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

            return true;
        } else {
            return false;
        }
    }

    public function getFileMaps()
    {
        return $this->hasMany(MapGroupFile::className(), ['groupID' => 'ID']);
    }

    public function getFiles()
    {
        return $this->hasMany(File::className(), ['ID' => 'fileID'])
            ->via('fileMaps');
    }
}
