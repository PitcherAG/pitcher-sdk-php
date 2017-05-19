<?php

namespace Pitcher\models;

use app\helpers\Tmp;
use Aws\Sdk;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%manifest_file}}".
 *
 * @property integer $id
 * @property string $app_id
 * @property string $name
 * @property string $path
 * @property integer $size
 * @property string $content_type
 * @property string $href
 * @property integer $user
 * @property string $ext
 * @property string $tmpFile
 * @property string $uploadedFile
 * @property string $type
 * @property PitApp $app
 */
class ManifestFile extends ActiveRecord
{
    public $ext;
    public $tmpFile;
    public $uploadedFile;
    public $fileID;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%manifest_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['size'], 'integer'],
            [['name', 'content_type', 'href'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'size' => 'Size',
            'content_type' => 'Content Type',
            'href' => 'Href',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at'
                ],
                'value' => function () {
                    return new Expression('NOW()');
                }
            ]
        ];
    }

    /**
     * @param string|null $bucket
     * @return \Aws\Result
     */
    public function saveAsTmp($bucket = null)
    {
        $this->tmpFile = Tmp::file(false);
        $s3 = (new Sdk)->createMultiRegionS3(Yii::$app->params["s3"]["vayen"]);

        // Save object to a file.
        return $s3->getObject([
            'Bucket' => $bucket?:$this->app->bucket,
            'Key' => $this->href,
            'SaveAs' => $this->tmpFile
        ]);
    }

    /**
     * @return \Aws\Result
     */
    public function deleteObject()
    {
        $s3 = (new Sdk)->createMultiRegionS3(Yii::$app->params["s3"]["vayen"]);

        return $s3->deleteObject(array(
            'Bucket' => $this->app->bucket,
            'Key' => $this->href
        ));
    }

    /**
     * @return string
     */
    public function getType()
    {
        switch ($this->ext) {
            case 'pdf':
                return 'pdf';
            case 'mov':
            case 'avi':
            case 'mpg':
            case 'mp4':
            case 'm4v':
            case 'mp3':
            case 'wav':
            case 'wma':
                return 'video';
            case 'ppt':
            case 'pptx':
            case 'key':
                return 'presentation';
            case "zip":
                return 'zip';
            default:
                return $this->ext;
        }
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApp()
    {
        return $this->hasOne(PitApp::className(), ['ID' => 'app_id']);
    }
}
