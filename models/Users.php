<?php

namespace app\models;

use Yii;

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
 * @property MpWallet[] $mpWallets
 */
class Users extends \yii\db\ActiveRecord
{
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
}
