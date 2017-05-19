<?php

namespace Pitcher\models;

use Intercom\IntercomClient;
use nineinchnick\usr\components;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{users}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $firstname
 * @property string $lastname
 * @property string $one_time_password_secret
 * @property string $one_time_password_code
 * @property integer $one_time_password_counter
 * @property string $activationKey
 * @property string $authKey
 * @property string $accessToken
 * @property string $role
 * @property string $pitcherRole
 * @property string $userType
 * @property string $federation_id
 * @property integer $enforceLicense
 * @property \datetime $passwordSetOn
 * @property Licensee[] $licensees
 *
 */
class User extends \yii\db\ActiveRecord
    implements
    components\IdentityInterface,
    components\EditableIdentityInterface,
    components\OneTimePasswordIdentityInterface,
    components\ActivatedIdentityInterface,
    components\PasswordHistoryIdentityInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // password is unsafe on purpose, assign it manually after hashing only if not empty
        return [
            ['email', 'required', 'when' => function () {
                return true;
            }, 'whenClient' => 'function (attribute, value) {
                var isUser = $("input[name=isUser]");

                if (isUser.length < 1)
                    return true;

                return isUser[0].checked;
            }'],

            [['username', 'password'], 'required'],
            [['username', 'email', 'firstname', 'lastname'], 'trim'],
            [['authKey', 'activationKey', 'accessToken', 'passwordSetOn'], 'trim', 'on' => 'search'],
            [['username', 'firstname', 'lastname'], 'default'],
            [['authKey', 'activationKey', 'accessToken', 'passwordSetOn'], 'default', 'on' => 'search'],
            [['authKey', 'activationKey', 'accessToken'], 'string', 'max' => 32, 'on' => 'search'],
            [['username', 'email'], 'unique', 'except' => 'search'],
            ['email', 'email'],
            [['passwordSetOn'], 'date', 'format' => ['yyyy-MM-dd', 'yyyy-MM-dd HH:mm', 'yyyy-MM-dd HH:mm:ss'], 'on' => 'search'],
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
            'role' => Yii::t('app', 'Role'),
            'firstname' => Yii::t('app', 'Firstname'),
            'lastname' => Yii::t('app', 'Lastname'),
            'email' => Yii::t('app', 'Email'),
            'authKey' => Yii::t('app', 'Auth Key'),
            'accessToken' => Yii::t('app', 'Access Token')
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->password = Yii::$app->getSecurity()->generateRandomString(6);
                $this->role = "administrator";
                $this->email = $this->username;
                $this->pitcherRole = "limited";
                $this->userType = "personal";
                $this->authKey = Yii::$app->getSecurity()->generateRandomString();
                $this->accessToken = Yii::$app->getSecurity()->generateRandomString();

                $client = new IntercomClient("ccec386q", "a8f1cd6ca9b50f865800e38d3d4537ad8bcb1e00");
                $response = $client->users->create([
                    'email' => $this->email,
                    'name' => $this->firstname . " " . $this->lastname,
                    'signed_up_at' => time()
                ]);
                $this->federation_id = $response->id;
            }

            return true;
        }

        return false;
    }

    /**
     * Finds an identity by the given username.
     *
     * @param  string $username the username to be looked for
     * @return components\IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findByUsername($username)
    {
        return self::findOne(['username' => $username]);
    }

    /**
     * @param  string $password password to validate
     * @return bool   if password provided is valid for current user
     */
    public function verifyPassword($password)
    {
        return $password == $this->password;
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param  string|integer $id the ID to be looked for
     * @return components\IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * Finds an identity by the given secrete token.
     *
     * @param  string $token the secrete token
     * @param  mixed $type the type of the token. The value of this parameter depends on the implementation.
     * @return components\IdentityInterface     the identity object that matches the given token.
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @param  string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function authenticate($password)
    {
        if (!$this->verifyPassword($password)) {
            return [self::ERROR_INVALID, Yii::t('usr', 'Invalid username or password.')];
        }

        $this->save(false);

        return true;
    }

    // }}}

    // {{{ EditableIdentityInterface

    /**
     * Maps the \nineinchnick\usr\models\ProfileForm attributes to the identity attributes
     * @see \nineinchnick\usr\models\ProfileForm::attributes()
     * @return array
     */
    public function identityAttributesMap()
    {
        // notice the capital N in name
        return ['username' => 'username', 'email' => 'email', 'firstName' => 'firstname', 'lastName' => 'lastname'];
    }

    /**
     * Saves a new or existing identity. Does not set or change the password.
     * @see PasswordHistoryIdentityInterface::resetPassword()
     * Should detect if the email changed and mark it as not verified.
     * @param  boolean $requireVerifiedEmail
     * @return boolean
     */
    public function saveIdentity($requireVerifiedEmail = false)
    {
        if ($this->isNewRecord) {
            $this->password = 'x';
        }
        if (!$this->save()) {
            Yii::warning('Failed to save user: ' . print_r($this->getErrors(), true), 'usr');

            return false;
        }

        return true;
    }

    /**
     * Sets attributes like username, email, first and last name.
     * Password should be changed using only the resetPassword() method from the PasswordHistoryIdentityInterface.
     * @param  array $attributes
     * @return boolean
     */
    public function setIdentityAttributes(array $attributes)
    {
        $allowedAttributes = $this->identityAttributesMap();
        foreach ($attributes as $name => $value) {
            if (isset($allowedAttributes[$name])) {
                $key = $allowedAttributes[$name];
                $this->$key = $value;
            }
        }

        return true;
    }

    /**
     * Returns attributes like username, email, first and last name.
     * @return array
     */
    public function getIdentityAttributes()
    {
        $allowedAttributes = array_flip($this->identityAttributesMap());
        $result = [];
        foreach ($this->getAttributes() as $name => $value) {
            if (isset($allowedAttributes[$name])) {
                $result[$allowedAttributes[$name]] = $value;
            }
        }

        return $result;
    }

    // }}}

    // {{{ OneTimePasswordIdentityInterface

    /**
     * Returns current secret used to generate one time passwords. If it's null, two step auth is disabled.
     * @return string
     */
    public function getOneTimePasswordSecret()
    {
        return $this->one_time_password_secret;
    }

    /**
     * Sets current secret used to generate one time passwords. If it's null, two step auth is disabled.
     * @param  string $secret
     * @return boolean
     */
    public function setOneTimePasswordSecret($secret)
    {
        if ($this->getIsNewRecord()) {
            return false;
        }
        $this->one_time_password_secret = $secret;

        return $this->save(false);
    }

    /**
     * Returns previously used one time password and value of counter used to generate current one time password, used in counter mode.
     * @return array [string, integer]
     */
    public function getOneTimePassword()
    {
        return [
            $this->one_time_password_code,
            $this->one_time_password_counter === null ? 1 : $this->one_time_password_counter,
        ];
    }

    /**
     * Sets previously used one time password and value of counter used to generate current one time password, used in counter mode.
     * @return boolean
     */
    public function setOneTimePassword($password, $counter = 1)
    {
        if ($this->getIsNewRecord()) {
            return false;
        }
        $this->one_time_password_code = $password;
        $this->one_time_password_counter = $counter;

        return $this->save(false);
    }

    public function checkSSE()
    {
        return ($this->enforceLicense == 1);
    }

    public function subscriptionValid()
    {

        $license = Licensee::findOne(['adminID' => $this->id]);
        if ($license) {
            if (strtotime($license->licenseValidUntil) > time()) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * @return ActiveQuery
     */
    public function getApps()
    {
        return $this->hasMany(PitApp::className(), ['ID' => 'appID'])
            ->viaTable('map_users_apps', ['userID' => 'id']);
    }

    /**
     * Checkes if user account is active. This should not include disabled (banned) status.
     * This could include if the email address has been verified.
     * Same checks should be done in the authenticate() method, because this method is not called before logging in.
     * @return boolean
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Checkes if user account is disabled (banned). This should not include active status.
     * @return boolean
     */
    public function isDisabled()
    {
        return false;
    }

    /**
     * Checkes if user email address is verified.
     * @return boolean
     */
    public function isVerified()
    {
        return true;
    }

    /**
     * Generates and saves a new activation key used for verifying email and restoring lost password.
     * The activation key is then sent by email to the user.
     *
     * Note: only the last generated activation key should be valid and an activation key
     * should have it's generation date saved to verify it's age later.
     *
     * @return string
     */
    public function getActivationKey()
    {
        $this->activationKey = Yii::$app->security->generateRandomString();
        return $this->save(false) ? $this->activationKey : false;
    }

    /**
     * Verifies if specified activation key matches the saved one and if it's not too old.
     * This method should not alter any saved data.
     * @return integer the verification error code. If there is an error, the error code will be non-zero.
     */
    public function verifyActivationKey($activationKey)
    {
        return $this->activationKey === $activationKey ? self::ERROR_AKEY_NONE : self::ERROR_AKEY_INVALID;
    }

    /**
     * Verify users email address, which could also activate his account and allow him to log in.
     * Call only after verifying the activation key.
     * @param  boolean $requireVerifiedEmail
     * @return boolean
     */
    public function verifyEmail($requireVerifiedEmail = false)
    {
        return true;
    }

    /**
     * Returns the date when specified password was last set or null if it was never used before.
     * If null is passed, returns date of setting current password.
     * @param  string $password new password or null if checking when the current password has been set
     * @return string date in YYYY-MM-DD format or null if password was never used.
     */
    public function getPasswordDate($password = null)
    {
        if ($password === null) {
            return $this->passwordSetOn;
        } else {
            return null;
        }
    }

    /**
     * Changes the password and updates last password change date.
     * Saves old password so it couldn't be used again.
     * @param  string $password new password
     * @return boolean
     */
    public function resetPassword($password)
    {
        $this->setAttributes([
            'password' => $password,
            'passwordSetOn' => date('Y-m-d H:i:s'),
        ], false);

        return $this->save();
    }


    public function getLicensees()
    {
        return $this->hasMany(Licensee::className(), ['adminID' => 'id']);
    }
}