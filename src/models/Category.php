<?php

namespace Pitcher\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_app_categories".
 *
 * @property integer $id
 * @property string $categoryName
 * @property string $appID
 * @property string $parentCategory
 * @property string $relatedType
 * @property string $isContainAll
 */
class Category extends ActiveRecord
{   
    public static function tableName()
    {
        return 'tbl_app_categories';
    }
    /**
     * @inheritdoc
     * @return static ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function findOneWithHidden($condition)
    {
        if ($condition == "0" || $condition == 0) {
            $category = new Category();
            $category->id = $condition;
            $category->categoryName = \Yii::t("app", "--Hidden--");
            return $category;
        }
        return static::findOne($condition);
    }

}