<?php

namespace Pitcher\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class App extends ActiveRecord
{   
    public static function tableName()
    {
        return '{{%apps}}';
    }

    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['appID' => 'ID']);
    }

    public function getFiles()
    {
        return $this->hasMany(File::className(), ['appID' => 'ID']);
    }

    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['id' => 'device_id'])
            ->viaTable('map_device_app', ['app_id' => 'ID']);
    }

    /**
     * @return \yii\db\ActiveQuery $users
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'userID'])
            ->viaTable('map_users_apps', ['appID' => 'ID']);
    }

    public function getMetadata()
    {
        $devices = $this->getDevices()->select("metadata")->all();
        $metadata = [];

        foreach ($devices as $device)
        {
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

        foreach ($files as $file)
        {
            $keywords = array_merge($keywords, array_map('trim', explode(",", $file->keywords)));
        }

        $devices = $this->getDevices()->select("metadata")->all();

        foreach ($devices as $device)
        {
            $keywords = array_merge($keywords, array_map('trim', explode(",", $device->metadata)));
        }

        $keywords = array_filter(array_unique($keywords));
        sort($keywords, SORT_NATURAL ^ SORT_FLAG_CASE);

        return $keywords;
    }

    public function getMapApps()
    {
        return $this->hasMany(MapDeviceApp::className(), ['app_id' => 'ID']);
    }
}