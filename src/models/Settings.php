<?php

namespace Pitcher\models;

use Exception;
use stdClass;

/**
 * This is the model class for table "{{%settings}}".
 *
 * @property integer $ID
 * @property integer $appID
 * @property string $extraField
 * @property string $statsEnabled
 * @property string $systemLang
 * @property string $uiColorBack
 * @property string $uiColorBar
 * @property string $vVersion
 * @property string $uiFontColor
 * @property string $uiFontColorAlt
 * @property string $templateID
 * @property integer $gpsTrackingEnabled
 * @property integer $showPointMarkers
 * @property integer $defaultExpirationPeriod
 * @property string $analyticsID
 * @property string $appVersion
 * @property string $appVersionMessage
 * @property string $appVersionUpdateURL
 * @property integer $threeGOnly
 * @property string $crmURL
 * @property integer $enable_chapter
 * @property string $ui_template_text
 * @property string $emailBody
 * @property string $emailSubject
 * @property string $crmExtra
 * @property string $zurmoVersion
 * @property integer $anonymousDetails
 * @property integer $versioningEnabled
 * @property integer $shouldLogoutV
 * @property string $conversionFolder
 * @property string $currency
 * @property integer $pageSwipePoints
 * @property integer $connectEnabled
 * @property integer $connectShowOnCMS
 * @property integer $noCRM
 * @property string $connectTemplateID
 * @property integer $pdfEnabled
 * @property integer $fallbackTemplate
 * @property string $jiraSettings
 * @property string $connectURL
 * @property integer $quizEnabled
 * @property string $connectURLCopy
 * @property integer $appStoreOnly
 * @property stdClass $extra
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appID', 'statsEnabled', 'systemLang', 'uiColorBack', 'uiColorBar'], 'required'],
            [['appID', 'gpsTrackingEnabled', 'showPointMarkers', 'defaultExpirationPeriod', 'threeGOnly', 'enable_chapter', 'anonymousDetails', 'versioningEnabled', 'shouldLogoutV', 'pageSwipePoints', 'connectEnabled', 'connectShowOnCMS', 'noCRM', 'pdfEnabled', 'fallbackTemplate', 'quizEnabled', 'appStoreOnly'], 'integer'],
            [['extraField', 'ui_template_text', 'emailBody', 'emailSubject', 'crmExtra'], 'string'],
            [['vVersion'], 'safe'],
            [['statsEnabled', 'systemLang', 'uiColorBack', 'uiColorBar', 'uiFontColor', 'uiFontColorAlt', 'templateID', 'analyticsID', 'appVersionMessage', 'appVersionUpdateURL', 'crmURL', 'currency', 'connectTemplateID', 'jiraSettings', 'connectURL', 'connectURLCopy'], 'string', 'max' => 255],
            [['appVersion'], 'string', 'max' => 15],
            [['zurmoVersion'], 'string', 'max' => 11],
            [['conversionFolder'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'appID' => 'App ID',
            'extraField' => 'Extra Field',
            'statsEnabled' => 'Stats Enabled',
            'systemLang' => 'System Lang',
            'uiColorBack' => 'Ui Color Back',
            'uiColorBar' => 'Ui Color Bar',
            'vVersion' => 'V Version',
            'uiFontColor' => 'Ui Font Color',
            'uiFontColorAlt' => 'Ui Font Color Alt',
            'templateID' => 'Template ID',
            'gpsTrackingEnabled' => 'Gps Tracking Enabled',
            'showPointMarkers' => 'Show Point Markers',
            'defaultExpirationPeriod' => 'Default Expiration Period',
            'analyticsID' => 'Analytics ID',
            'appVersion' => 'App Version',
            'appVersionMessage' => 'App Version Message',
            'appVersionUpdateURL' => 'App Version Update Url',
            'threeGOnly' => 'Three Gonly',
            'crmURL' => 'Crm Url',
            'enable_chapter' => 'Enable Chapter',
            'ui_template_text' => 'Ui Template Text',
            'emailBody' => 'Email Body',
            'emailSubject' => 'Email Subject',
            'crmExtra' => 'Crm Extra',
            'zurmoVersion' => 'Zurmo Version',
            'anonymousDetails' => 'Anonymous Details',
            'versioningEnabled' => 'Versioning Enabled',
            'shouldLogoutV' => 'Should Logout V',
            'conversionFolder' => 'Conversion Folder',
            'currency' => 'Currency',
            'pageSwipePoints' => 'Page Swipe Points',
            'connectEnabled' => 'Connect Enabled',
            'connectShowOnCMS' => 'Connect Show On Cms',
            'noCRM' => 'No Crm',
            'connectTemplateID' => 'Connect Template ID',
            'pdfEnabled' => 'Pdf Enabled',
            'fallbackTemplate' => 'Fallback Template',
            'jiraSettings' => 'Jira Settings',
            'connectURL' => 'Connect Url',
            'quizEnabled' => 'Quiz Enabled',
            'connectURLCopy' => 'Connect Urlcopy',
            'appStoreOnly' => 'App Store Only',
        ];
    }

    public function getExtra()
    {
        try {
            $res = json_decode($this->extraField);
        } catch (Exception $e) {
            $res = new stdClass();
        }

        return $res;
    }
}
