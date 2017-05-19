<?php

namespace Pitcher\models;

use Yii;

/**
 * This is the model class for table "{{%ipadusers}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $generatedID
 * @property string $metadata
 * @property string $email
 */
class IPadUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ipadusers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username'], 'string', 'max' => 100],
            [['password', 'generatedID'], 'string', 'max' => 50],
            [['metadata', 'email'], 'string', 'max' => 255],
            [['username', 'password'], 'unique', 'targetAttribute' => ['username', 'password'], 'message' => 'The combination of Username and Password has already been taken.'],
            [['username'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'generatedID' => Yii::t('app', 'Generated ID'),
            'metadata' => Yii::t('app', 'Metadata'),
            'email' => Yii::t('app', 'Email'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->generatedID = sha1($this->username . $this->password);
                $this->password = md5($this->password);
            }

            return true;
        }

        return false;
    }
}
