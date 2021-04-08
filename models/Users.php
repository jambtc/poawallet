<?php

namespace app\models;

use Yii;
use app\models\Auth;

/**
 * This is the model class for table "mp_users".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $ga_secret_key
 * @property string $activation_code
 * @property int $status_activation_code
 * @property string $oauth_provider
 * @property string $oauth_uid
 * @property string|null $authKey
 * @property string|null $accessToken
 * @property string|null $facade
 * @property string|null $provider
 * @property string|null $picture
 * @property string|null $email
 * @property string|null $last_name
 * @property string|null $first_name
 *
 * @property MPWallet[] $mpWallets
 */
class Users extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_INSERTED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mp_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'activation_code', 'status_activation_code', 'oauth_provider', 'oauth_uid'], 'required'],
            [['status_activation_code'], 'integer'],
            [['username', 'password', 'authKey', 'accessToken', 'picture', 'email', 'last_name', 'first_name'], 'string', 'max' => 255],
            [['ga_secret_key'], 'string', 'max' => 16],
            [['activation_code'], 'string', 'max' => 50],
            [['oauth_provider'], 'string', 'max' => 8],
            [['oauth_uid'], 'string', 'max' => 100],
            [['facade', 'provider'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'ga_secret_key' => Yii::t('app', 'Ga Secret Key'),
            'activation_code' => Yii::t('app', 'Activation Code'),
            'status_activation_code' => Yii::t('app', 'Status Activation Code'),
            'oauth_provider' => Yii::t('app', 'Oauth Provider'),
            'oauth_uid' => Yii::t('app', 'Oauth Uid'),
            'authKey' => Yii::t('app', 'Auth Key'),
            'accessToken' => Yii::t('app', 'Access Token'),
            'facade' => Yii::t('app', 'Facade'),
            'provider' => Yii::t('app', 'Provider'),
            'picture' => Yii::t('app', 'Picture'),
            'email' => Yii::t('app', 'Email'),
            'last_name' => Yii::t('app', 'Last Name'),
            'first_name' => Yii::t('app', 'First Name'),
        ];
    }

    /**
     * Gets query for [[MpWallets]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\MpWalletQuery
     */
    public function getMpWallets()
    {
        return $this->hasMany(MpWallet::className(), ['id_user' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\UsersQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function beforeSave($insert) {
        if(isset($this->password))
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);

        return parent::beforeSave($insert);
    }

    public function getAuths()
    {
        return $this->hasMany(Auth::className, ['user_id' => 'id']);
    }

    private function setAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString(60);
    }

    private function setUid()
    {
        $this->oauth_uid = Yii::$app->security->generateRandomString(60);
    }

    public function activate()
    {
        $this->status_activation_code = self::STATUS_ACTIVE;
        $this->setUid();
        return $this->save();
    }
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
      return self::findOne(['accessToken'=>$token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::findOne(['username'=>$username]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findUserByProvider($username,$oauth_provider)
    {

        // echo "<pre>".print_r($username,true)."</pre>";
        // echo "<pre>".print_r($oauth_provider,true)."</pre>";
		// exit;
        $record = self::findOne([
            'username'=>$username,
            'oauth_provider'=>$oauth_provider,
        ]);

        return $record;

    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        // echo $password;
        // echo '<br>'.$this->password;
        // exit;
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'activation_code' => $token,
            'status_activation_code' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password; // l'hash viene generato befor-save
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->activation_code = 0;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }
    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->activation_code = Yii::$app->security->generateRandomString() . '_' . time();
    }

}
