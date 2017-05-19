<?php

namespace Pitcher\models;

use app\helpers\Tmp;
use Aws\S3\Transfer;
use Aws\Sdk;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\HttpException;
use ZipArchive;

/**
 * This is the model class for table "{{%files}}".
 *
 * @property integer $ID
 * @property string $body
 * @property string $author
 * @property string $keywords
 * @property string $referencesV
 * @property string $typeV
 * @property string $biblio
 * @property string $filename
 * @property integer $year
 * @property integer $statusV
 * @property string $edit_time
 * @property string $category
 * @property integer $appID
 * @property \app\models\PitApp $app
 * @property string $extra
 * @property string $creation_time
 * @property string $extra2
 * @property string $server
 * @property integer $uploaded_by
 * @property string $original_name
 * @property string $pushNotificationText
 * @property integer $originalSize
 * @property integer $convertedSize
 * @property boolean $uploadOriginal
 * @property boolean $allowEditing
 * @property boolean $allowEmail
 * @property boolean $allowCustomEmail
 * @property boolean $allowPrinting
 * @property boolean $showFirstTwoPages
 * @property Distribution $distribution
 * @property Category $navigation
 * @property string $dir
 * @property string $key
 * @property Category $subNavigation
 * @property bool $enableMix
 * @property \app\models\QuizSurveySettings $quizSurveySettings
 * @property \app\models\PdfHotspots $pdfHotspots
 * @property User $uploadedBy
 */
class File extends ActiveRecord
{
    public $uploadOriginal;
    public $allowEditing;
    public $allowEmail;
    public $allowCustomEmail;
    public $allowPrinting;
    public $showFirstTwoPages;
    public $extractedTo;

    public function getApp()
    {
        return $this->hasOne(PitApp::className(), ['ID' => 'appID']);
    }

    public function getNavigation()
    {
        $ids = explode("|", $this->typeV);
        return Category::findOneWithHidden($ids[0]);
    }

    public function getSubNavigation()
    {
        $ids = explode("|", $this->typeV);

        if (count($ids) < 2)
            return null;

        return Category::findOne($ids[1]);
    }

    public function getCategoryRecord()
    {
        return $this->hasOne(Category::className(), ['ID' => 'typeV']);
    }

    public function getUploadedBy()
    {
        return $this->hasOne(User::className(), ['ID' => 'uploaded_by']);
    }

    public function getDistribution()
    {
        return $this->hasOne(Distribution::className(), ['fileID' => 'ID']);
    }

    public function getMetadata()
    {
        return $this->hasMany(FileMetadata::className(), ['fileID' => 'ID']);
    }

    public function getGroupMaps()
    {
        return $this->hasMany(MapGroupFile::className(), ['fileID' => 'ID']);
    }

    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['ID' => 'groupID'])
            ->via('groupMaps');
    }

    public function getDir()
    {
        switch ($this->category) {
            case 'pdf':
            case 'video':
                return "{$this->category}s";
            case 'presentation':
                return 'slides';
            case 'zip':
            case 'images':
            case 'surveys':
            case 'json':
            default:
                return $this->category;
                break;
        }
    }

    public function getKey()
    {
        return "{$this->dir}/{$this->filename}";
    }

    /**
     * @param string $key
     * @return FileMetadata
     */
    public function getMetadataWithKey($key)
    {
        $kv = ["key" => $key, "fileID" => $this->ID];
        $data = FileMetadata::find()->where($kv)->one();

        if (!$data) {
            $data = new FileMetadata();
            $data->setAttributes($kv);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getEnableMix()
    {
        $metadata = $this->getMetadataWithKey("enableMix");

        if ($metadata->id) {
            $enable = $metadata->value;
        } else {
            $enable = $this->app->defaultEnableMix;
        }

        return $enable;
    }

    public function getQuizSurveySettings()
    {
        return $this->hasOne(QuizSurveySettings::className(), ['fileID' => 'ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPdfHotspots()
    {
        return $this->hasOne(PdfHotspots::className(), ['pdfID' => 'ID']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body', 'typeV', 'year', 'statusV', 'appID'], 'required'],
            [['body', 'keywords', 'referencesV', 'extra2', 'original_name'], 'string'],
            [['year', 'statusV', 'appID', 'uploaded_by', 'originalSize', 'convertedSize'], 'integer'],
            [['edit_time', 'creation_time'], 'safe'],
            [['author', 'typeV', 'biblio', 'filename', 'category', 'extra', 'server', 'pushNotificationText'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'body' => 'Body',
            'author' => 'Author',
            'keywords' => 'Keywords',
            'referencesV' => 'References V',
            'typeV' => 'Type V',
            'biblio' => 'Biblio',
            'filename' => 'Filename',
            'year' => 'Year',
            'statusV' => 'Status V',
            'edit_time' => 'Edit Time',
            'category' => 'Category',
            'appID' => 'App ID',
            'extra' => 'Extra',
            'creation_time' => 'Creation Time',
            'extra2' => 'Extra2',
            'server' => 'Server',
            'uploaded_by' => 'Uploaded By',
            'original_name' => 'Original Name',
            'pushNotificationText' => 'Push Notification Text',
            'originalSize' => 'Original Size',
            'convertedSize' => 'Converted Size',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        if ($this->category == "presentation" || $this->category == "pdf") {

            if ($this->category == "presentation") {
                $this->uploadOriginal = $this->getMetadataWithKey("sendOriginalFile")->value;
                $this->allowEditing = $this->extra2 == "1";
                $this->allowEmail = $this->getMetadataWithKey("sendPDF")->value;
            } else if ($this->category == "pdf") {
                $pieces = explode("|", $this->extra2);
                $this->allowPrinting = $pieces[0] == "1";
                $this->allowEmail = count($pieces) < 2 ?: $pieces[1] == "1";
                $this->showFirstTwoPages = $this->year == "1";
                $this->allowEditing = $this->enableMix;
            }

            $this->allowCustomEmail = $this->getMetadataWithKey("allowCustomEmail")->value;
        } else {
            $data = $this->getMetadataWithKey("sendPDF");

            if ($data->id) {
                $this->allowEmail = $data->value;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->keywords) {

                /*
                 * separate keywords with comma and spaces
                 * i.e.: a, b, c
                 */
                $this->keywords = implode(', ',
                    array_map(function ($a) {
                        return trim($a);
                    }, explode(',', $this->keywords)));
            }

            if ($this->category == "presentation") {
                if (!$this->allowEditing) {
                    $this->allowEditing = 0;
                }
                $this->extra2 = "" . $this->allowEditing;
            } else if ($this->category == "pdf") {
                $this->extra2 = $this->allowPrinting . "|" . $this->allowEmail;
                $this->year = $this->showFirstTwoPages;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if ($this->quizSurveySettings) {
            $linkedFiles = File::find()
                ->where(["filename" => $this->filename])
                ->andWhere(["<>", "ID", $this->ID])
                ->all();

            foreach ($linkedFiles as $linkedFile)
            {
                if (!$linkedFile->delete()) {
                    return false;
                }
            }
        }

        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->category == "presentation" || $this->category == "pdf") {
            if ($this->category == "presentation") {
                $this->saveMetadataWithTypeBoolean("sendOriginalFile", $this->uploadOriginal);
            } else if ($this->category == "pdf") {
                $this->saveMetadataWithTypeBoolean("enableMix", $this->allowEditing);
            }

            $this->saveMetadataWithTypeBoolean("sendPDF", $this->allowEmail);
            $this->saveMetadataWithTypeBoolean("allowCustomEmail", $this->allowCustomEmail);
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool|integer
     */
    public function renameKeyword($from, $to)
    {
        $keywords = $this->keywords
            ? array_map('trim', explode(',', $this->keywords))
            : [];

        $key = array_search($from, $keywords);

        if ($key !== false) {
            $keywords[$key] = $to;
            $this->keywords = implode(', ', $keywords);
        }

        return $key;
    }

    public function download($dest = null) {
        $dest = $dest?: Tmp::file();
        $client = (new Sdk)->createMultiRegionS3(Yii::$app->params["s3"]["vayen"]);

        $client->getObject([
            'Bucket' => $this->app->bucket,
            'Key' => $this->dir . '/' . $this->filename,
            'SaveAs' => $dest,
        ]);

        return $dest;
    }

    public function extract($dest = null) {
        if ($dest || !$this->extractedTo) {
            $this->extractedTo = $dest ?: Tmp::dir();
            $filename = $this->download();
            $zip = new ZipArchive;

            if ($zip->open($filename) === TRUE) {
                $zip->extractTo($this->extractedTo);
                $zip->close();
            } else {
                throw new HttpException(500, 'Unable to extract file.');
            }
        }

        return $this->extractedTo;
    }

    /**
     * @param bool $tree
     * @return array
     */
    public function getContents($tree = true)
    {
        $client = (new Sdk)->createMultiRegionS3(Yii::$app->params["s3"]["vayen"]);
        $key = 'files/' . $this->ID . '/contents/';

        $creationTime = $this->getMetadataWithKey($key);

        if ($this->creation_time != $creationTime->value) {

            $source = $this->extract()  . '/' . pathinfo($this->filename, PATHINFO_FILENAME);
            $dest = 's3://' . $this->app->bucket . '/' . $key;

            // delete old contents
            $response = $client->listObjects([
                'Bucket' => $this->app->bucket,
                'Prefix' => $key
            ]);

            $keys = array_map(function ($content) {
                return ['Key' => $content['Key']];
            }, $response['Contents']?: []);

            if (count($keys) > 0) {
                $client->deleteObjects([
                    'Bucket' => $this->app->bucket,
                    'Delete' => [
                        'Objects' => $keys,
                        'Quiet' => true
                    ]
                ]);
            }

            // upload contents
            $transfer = new Transfer($client, $source, $dest, ['mup_threshold' => 2*1024*1024*1024]);
            $transfer->transfer();

            $creationTime->value = $this->creation_time;
            $creationTime->save();
        }

        $response = $client->listObjects([
            'Bucket' => $this->app->bucket,
            'Prefix' => $key
        ]);

        if ($tree) {
            return $this->getContentsTree($key, $response);
        } else {
            return $this->getContentsFlat($key, $response);
        }
    }

    /**
     * @param string $key
     * @param \Aws\Result $response
     * @return array
     */
    private function getContentsTree($key, $response)
    {
        $rootFilename = pathinfo($this->filename, PATHINFO_FILENAME);

        /** @var ContentFolder $root */
        $root = new ContentFolder();
        $root->name = $rootFilename;
        $root->path = $rootFilename;

        foreach ($response['Contents'] as $content) {
            $path = substr($content['Key'], strlen($key));
            $dirname = pathinfo($path, PATHINFO_DIRNAME);
            $basename = pathinfo($path, PATHINFO_BASENAME);
            $size = $content['Size'];
            $parent = $root;

            if ($dirname != '.') {
                // find or create parent folder
                $pieces = explode('/', $dirname);

                for ($i = 0; $i < count($pieces); $i++) {
                    $folderName = $pieces[$i];
                    $folderPath = implode('/', array_slice($pieces, 0, $i + 1));

                    $matches = array_values(array_filter($parent->getContents(), function ($c) use ($folderName) {
                        /** @var Content $c */
                        return $c->name == $folderName && $c->getType() == 'folder';
                    }));

                    if (count($matches) == 0) {
                        $folder = new ContentFolder();
                        $folder->name = $pieces[$i];
                        $folder->path = $rootFilename . '/' . $folderPath;
                        $parent->addContent($folder);
                    } else {
                        $folder = $matches[0];
                    }

                    $parent = $folder;
                }
            }

            $file = new ContentFile();
            $file->name = $basename;
            $file->path = $rootFilename . '/' . $path;
            $file->size = $size;
            $parent->addContent($file);
        }

        return $root->toArray();
    }

    /**
     * @param string $key
     * @param \Aws\Result $response
     * @return array
     */
    private function getContentsFlat($key, $response)
    {
        $root = pathinfo($this->filename, PATHINFO_FILENAME);

        return array_map(function($content) use ($key, $root) {
            $path = substr($content['Key'], strlen($key));

            return [
                'name' => pathinfo($path, PATHINFO_BASENAME),
                'path' => $root . '/' . $path,
                'size' => $content['Size']
            ];
        }, $response['Contents']);
    }

    /**
     * @param $path
     * @return mixed|null
     */
    public function getContentBody($path) {
        $dirs = explode('/', $path);

        if ($dirs[0] == pathinfo($this->filename, PATHINFO_FILENAME)) {
            array_shift($dirs);
        }

        $s3 = (new Sdk)->createMultiRegionS3(Yii::$app->params['s3']['vayen']);

        return $s3->getObject([
            'Bucket' => $this->app->bucket,
            'Key' => 'files/' . $this->ID . '/contents/' . implode('/', $dirs)
        ])->get('Body');
    }

    /**
     * @param $path
     * @param array $args
     * @return string
     */
    public function getContentUrl($path, $args = []) {
        $dirs = explode('/', $path);

        if ($dirs[0] == pathinfo($this->filename, PATHINFO_FILENAME)) {
            array_shift($dirs);
        }

        $s3 = (new Sdk)->createMultiRegionS3(Yii::$app->params['s3']['vayen']);

        $cmd = $s3->getCommand('GetObject', $args + [
            'Bucket' => $this->app->bucket,
            'Key' => 'files/' . $this->ID . '/contents/' . implode('/', $dirs)
        ]);

        return (string)$s3->createPresignedRequest($cmd, '+5 minutes')->getUri();
    }

    /**
     * @param $path
     * @param $body
     * @return bool|int
     */
    public function replaceContent($path, $body) {
        $extracted = $this->extract();
        $res = file_put_contents($extracted . '/' . $path, $body);
        return $res;
    }

    /**
     * @param $from
     * @param $to
     * @return bool
     */
    public function renameContent($from, $to)
    {
        $extracted = $this->extract();
        $res = rename($extracted . '/' . $from, $extracted . '/' . $to);
        return $res;
    }

    /**
     * @param $path
     * @return bool
     */
    public function deleteContent($path) {
        $extracted = $this->extract();
        $res = unlink($extracted . '/' . $path);
        return $res;
    }

    // TODO move to FileMetadata.php->_setter($key,$value)
    private function saveMetadataWithTypeBoolean($key, &$value) {
        if (!$value) {
            $value = 0;
        }

        $data = $this->getMetadataWithKey($key);
        $data->value = "" . $value;
        return $data->save();
    }

    /**
     * @param string $manifestFilePrefix
     * @param ManifestFile[] $manifestFiles
     * @param string $dirname
     * @return \Aws\Result
     */
    public function addManifestFiles($manifestFilePrefix, $manifestFiles, $dirname)
    {
        $extracted = $this->extract();

        // download and add manifests to file
        foreach ($manifestFiles as $manifestFile) {
            $manifestFile->saveAsTmp($this->app->bucket);
            rename($manifestFile->tmpFile, $extracted . '/' . $dirname . '/' . substr($manifestFile->path, strlen($manifestFilePrefix)));
        }
    }

    /**
     * @param $path
     * @return \Aws\Result
     * @throws HttpException
     */
    public function uploadDir($path)
    {
        $archive = Tmp::file();

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        // create archive
        $zip = new ZipArchive;

        if ($zip->open($archive, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $name => $file) {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($path) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }
        } else {
            throw new HttpException(500, 'Unable to extract file.');
        }

        $zip->close();

        return $this->uploadFile($archive);
    }

    /**
     * @return \Aws\Result
     */
    public function upload()
    {
        return $this->uploadDir($this->extractedTo);
    }

    /**
     * @param $archive
     * @return \Aws\Result
     */
    public function uploadFile($archive)
    {
        $client = (new Sdk)->createMultiRegionS3(Yii::$app->params["s3"]["vayen"]);

        $result = $client->putObject([
            'Bucket' => $this->app->bucket,
            'Key' => $this->dir . '/' . $this->filename,
            'SourceFile' => $archive,
            'ACL' => 'authenticated-read'
        ]);

        $this->edit_time = new Expression('NOW()');
        $this->creation_time = new Expression('NOW()');
        $this->save();

        return $result;
    }
}

class Content
{
    public $name;
    public $path;

    protected $_type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'type' => $this->_type
        ];
    }
}

class ContentFile extends Content
{
    public $size;

    function __construct()
    {
        $this->_type = 'file';
        $this->size = 0;
    }

    public function toArray()
    {
        return parent::toArray() + [
                'size' => $this->size
            ];
    }
}

class ContentFolder extends Content
{
    protected $_contents;

    function __construct()
    {
        $this->_type = 'folder';
        $this->_contents = [];
    }

    /**
     * @return Content[]
     */
    public function getContents()
    {
        return $this->_contents;
    }

    /**
     * @param Content $content
     * @return int $length
     */
    public function addContent($content)
    {
        return array_push($this->_contents, $content);
    }

    public function toArray()
    {
        return parent::toArray() + [
                'items' => array_map(
                    function ($content) {
                        /** @var Content $content */
                        return $content->toArray();
                    }, $this->_contents)
            ];
    }
}