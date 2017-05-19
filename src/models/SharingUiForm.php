<?php

namespace Pitcher\models;

use Aws\Sdk;
use stdClass;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class SharingUiForm
 * @package app\models
 * @property $app
 * @property $settings
 * @property $backgroundColor
 * @property $foregroundColor
 * @property $logo
 */
class SharingUiForm extends Model
{
    /** @var UploadedFile */
    public $logoFile;

    /** @var Settings */
    private $_settings;

    /** @var PitApp */
    private $_app;

    /** @var string */
    private $_backgroundColor;

    /** @var string */
    private $_foregroundColor;

    /** @var string */
    private $_logo;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['logoFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png'],
            [['backgroundColor', 'foregroundColor'], 'string'],
            [['settings', 'app'], 'required'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'logoFile' => Yii::t('app', 'Logo'),
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $extra = $this->_settings->extra;

            if (!$extra->sharing) {
                $extra->sharing = new stdClass();
            }

            if ($this->logoFile) {
                $key = 'logos/' . $this->app->ID . '.' . $this->logoFile->extension;

                $s3 = (new Sdk)->createMultiRegionS3(Yii::$app->params["s3"]["vayen"]);
                $s3->upload($this->app->bucket, $key, fopen($this->logoFile->tempName, 'r'), 'public-read');

                $this->_logo = $extra->sharing->logo = "https://{$this->app->bucket}.s3.amazonaws.com/{$key}";
            }

            $extra->sharing->backgroundColor = $this->_backgroundColor;
            $extra->sharing->foregroundColor = $this->_foregroundColor;

            $this->_settings->extraField = json_encode($extra);
            $this->_settings->save();

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param PitApp $app
     */
    public function setApp($app)
    {
        $this->_app = $app;
    }

    /**
     * @param string $color
     */
    public function setBackgroundColor($color)
    {
        $this->_backgroundColor = $color;
    }

    /**
     * @param string $color
     */
    public function setForegroundColor($color)
    {
        $this->_foregroundColor = $color;
    }

    /**
     * @param Settings $settings
     */
    public function setSettings($settings)
    {
        $this->_settings = $settings;

        if ($settings->extra && $settings->extra->sharing) {
            $sharing = $settings->extra->sharing;

            $this->_backgroundColor = $sharing->backgroundColor;
            $this->_foregroundColor = $sharing->foregroundColor;
            $this->_logo = $sharing->logo;
        }
    }

    /**
     * @return PitApp
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * @return string
     */
    public function getBackgroundColor()
    {
        if ($this->_backgroundColor) {
            return $this->_backgroundColor;
        } else {
            return '#FFFFFF';
        }
    }

    /**
     * @return string
     */
    public function getForegroundColor()
    {
        if ($this->_foregroundColor) {
            return $this->_foregroundColor;
        } else {
            return '#333333';
        }
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->_logo;
    }

    /**
     * @return Settings
     */
    public function getSettings()
    {
        return $this->_settings;
    }
}