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

class BoltLogin extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
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
            [['username', 'password', 'authKey', 'accessToken'], 'string', 'max' => 255],
            [['ga_secret_key'], 'string', 'max' => 16],
            [['activation_code'], 'string', 'max' => 50],
            [['oauth_provider'], 'string', 'max' => 8],
            [['oauth_uid'], 'string', 'max' => 100],

            // [['email'], 'string', 'max' => 100],
            // [['first_name', 'last_name'], 'string', 'max' => 255],
            // [['picture'], 'string', 'max' => 255],
        ];
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
        // return $this->authKey;
        return null; // update from yii framework 2.0.40 to 2.0.41
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
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

    //
    public function getAuths()
    {
        return $this->hasMany(Auth::className, ['user_id' => 'id']);
    }

}
