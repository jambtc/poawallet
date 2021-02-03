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
      $record = self::findOne([
        'username'=>$username,
        'oauth_provider'=>$oauth_provider,
      ]);

      if($record===null){
    		return null;
    	}	else {
        $social = BoltSocialUsers::find()
          ->where([
      			'oauth_uid'=>$record->oauth_uid,
      			'oauth_provider'=>$oauth_provider,
      		])
          ->one();

				// fix per salvare un social user nel caso non fosse stato salvato
				if (null === $social){
					$explodemail = explode('@',$username);
					$explodename = explode('.',$explodemail[0]);

					$social = new BoltSocialUsers();
					$social->oauth_provider = $oauth_provider;
					$social->oauth_uid = $record->oauth_uid;
					$social->id_user = $record->id_user;
					$social->first_name = $explodename[0];
					$social->last_name = isset($explodename[1]) ? $explodename[1] : '';
					$social->username = $explodemail[0];
					$social->email = $username;
					$social->picture = 'css/images/anonymous.png';

					$social->save();
				}

				$obj = (object) [
					'id_user' => $record->id,
					'name' => $social->first_name,
					'surname' => $social->last_name,
					'email' => $username,
					'username' => $social->username,
					'picture' => $social->picture,
					'provider'=> $social->oauth_provider,
					'oauth_uid'=> $record->oauth_uid,
					'facade' => 'dashboard',
				];

        Yii::$app->session->set('objUser',$obj);

        return $record;
  		}
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
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

}
