<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "map_file_category".
 *
 * @property integer $id
 * @property integer $fileID
 * @property integer $categoryID
 * @property integer $subCategoryID
 */
class MapFileCat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_file_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fileID', 'categoryID', 'subCategoryID'], 'integer'],
            [['categoryID', 'subCategoryID', 'fileID'], 'unique', 'targetAttribute' => ['categoryID', 'subCategoryID', 'fileID'], 'message' => 'The combination of File ID, Category ID and Sub Category ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fileID' => 'File ID',
            'categoryID' => 'Category ID',
            'subCategoryID' => 'Sub Category ID',
        ];
    }
}
