<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "{{%pdf_hotspots}}".
 *
 * @property integer $id
 * @property string $hotspots
 * @property integer $statusV
 * @property integer $pdfID
 */
class PdfHotspots extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pdf_hotspots}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hotspots'], 'string'],
            [['statusV', 'pdfID'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hotspots' => 'Hotspots',
            'statusV' => 'Status V',
            'pdfID' => 'Pdf ID',
        ];
    }
}
