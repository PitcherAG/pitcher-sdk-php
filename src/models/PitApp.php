<?php

namespace Pitcher\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\HttpException;

/**
 * @property string $ID
 * @property string $bucket
 * @property string $name
 * @property string[] $keywords
 * @property Group[] $groups
 * @property array $fileKeywords
 * @property \app\models\Topic[] $topics
 * @property \app\models\Settings $settings
 * @property \app\models\Device[] $devices
 * @property bool $defaultEnableMix
 */
class PitApp extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%apps}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['appID' => 'ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::className(), ['appID' => 'ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['id' => 'device_id'])
            ->viaTable('map_device_app', ['app_id' => 'ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppUsers()
    {
        return $this->hasMany(MapUserApp::className(), ['appID' => 'ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'userID'])
            ->via('appUsers');
    }

    public function getMetadata()
    {
        $devices = $this->getDevices()->select("metadata")->all();
        $metadata = [];

        foreach ($devices as $device) {
            $metadata = array_merge($metadata, array_map('trim', explode(",", $device->metadata)));
        }

        $metadata = array_filter(array_unique($metadata));
        sort($metadata, SORT_NATURAL ^ SORT_FLAG_CASE);

        return $metadata;
    }

    public function getKeywords()
    {
        $files = $this->getFiles()->select("keywords")->all();
        $keywords = [];

        // add every file's keywords
        foreach ($files as $file)
            $keywords = array_merge($keywords, array_map('trim', explode(",", $file->keywords)));

        // add every device's keywords
        $devices = $this->getDevices()->select("metadata")->all();
        foreach ($devices as $device)
            $keywords = array_merge($keywords, array_map('trim', explode(",", $device->metadata)));

        // add topics
        $topics = $this->getTopics()->select(["topic"])->distinct()->asArray()->column();
        $keywords = array_merge($keywords, $topics);

        $keywords = array_filter(array_unique($keywords));
        sort($keywords, SORT_NATURAL ^ SORT_FLAG_CASE);

        return $keywords;
    }

    public function getDeviceKeywords()
    {
        $keywords = [];

        // add every device's keywords
        $devices = $this->getDevices()->select("metadata")->all();
        foreach ($devices as $device)
            $keywords = array_merge($keywords, array_map('trim', explode(",", $device->metadata)));

        $keywords = array_filter(array_unique($keywords));
        sort($keywords, SORT_NATURAL ^ SORT_FLAG_CASE);

        return $keywords;
    }

    public function getFileKeywords()
    {
        $files = $this->getFiles()->select("keywords")->all();
        $keywords = [];

        // add every file's keywords
        foreach ($files as $file)
            $keywords = array_merge($keywords, array_map('trim', explode(",", $file->keywords)));

        // add topics
        $topics = $this->getTopics()->select(["topic"])->distinct()->asArray()->column();
        $keywords = array_merge($keywords, $topics);

        $keywords = array_filter(array_unique($keywords));
        sort($keywords, SORT_NATURAL ^ SORT_FLAG_CASE);

        return $keywords;
    }

    public function getMapApps()
    {
        return $this->hasMany(MapDeviceApp::className(), ['app_id' => 'ID']);
    }

    public function getSettings()
    {
        return $this->hasOne(Settings::className(), ['appID' => 'ID']);
    }

    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['appID' => 'ID']);
    }

    /**
     * @return array|\yii\db\ActiveQuery
     */
    public function getParentNavigationItems()
    {
        $navigation = $this->getCategories()->select(["id", "categoryName"])->where(['parentCategory' => 0])->orderBy('categoryName');
        return $navigation;
    }

    /**
     * @return array
     */
    public function getParentNavigationItemsWithHiddenItem()
    {
        $navigation = $this->getParentNavigationItems()->all();

        // navagation item: --Hidden--
        $hidden = new Category();
        $hidden->id = "0";
        $hidden->categoryName = \Yii::t("app", "--Hidden--");

        array_unshift($navigation, $hidden);

        return $navigation;
    }

    /**
     * @return array|\yii\db\ActiveQuery
     */
    public function getTopics()
    {
        return $this->hasMany(Topic::className(), ['appID' => 'ID']);
    }

    public function getDefaultEnableMix()
    {
        // get application default
        try {
            $res = json_decode($this->settings->extraField)->defaultDontMix !== true;
        } catch (\Exception $e) {
            $res = true;
        }

        return $res;
    }
}