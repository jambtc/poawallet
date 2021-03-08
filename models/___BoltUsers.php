<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bolt_users".
 *
 * @property int $id_user
 * @property string $email
 * @property string $password
 * @property string|null $ga_secret_key
 * @property string $activation_code
 * @property int $status_activation_code
 * @property string $oauth_provider
 * @property string $oauth_uid
 */
class BoltUsers extends \yii\db\ActiveRecord
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
            [['username', 'password', 'authKey', 'accessToken'], 'string', 'max' => 255],
            [['ga_secret_key'], 'string', 'max' => 16],
            [['activation_code'], 'string', 'max' => 50],
            [['oauth_provider'], 'string', 'max' => 8],
            [['oauth_uid'], 'string', 'max' => 100],

            // password is hashed
            ['password', 'hashIt'],
        ];
    }

    /**
  	 * Authenticates the password.
  	 * This is the 'authenticate' validator as declared in rules().
  	 * @param string $attribute the name of the attribute to be validated.
  	 * @param array $params additional parameters passed with rule when being executed.
  	 */
  	public function hashIt($attribute,$params)
  	{
        if ($this->isNewRecord)
          $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);

  	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id User'),
            'username' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'ga_secret_key' => Yii::t('app', 'Ga Secret Key'),
            'activation_code' => Yii::t('app', 'Activation Code'),
            'status_activation_code' => Yii::t('app', 'Status Activation Code'),
            'oauth_provider' => Yii::t('app', 'Oauth Provider'),
            'oauth_uid' => Yii::t('app', 'Oauth Uid'),
        ];
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
    public function getAuthKey()
    {
        return $this->authKey;
    }


    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

}
