<?php

namespace app\models;

use Yii;
use yii\base\Model;

use app\models\BoltUsers;
use app\models\BoltSocialsUsers;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    const ERROR_NONE = 0;
    const ERROR_USERNAME_INVALID = 1;
    const ERROR_USERNAME_NOT_ACTIVE = 3;
	  const ERROR_GOOGLE_NOT_AUTHENTICATE = 5;

    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    public $id_user;
    public $email;
    public $ga_secret_key;
    public $activation_code;
    public $status_activation_code;
    public $oauth_provider;
    public $oauth_uid;



    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }


    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findUserByProvider($username,$oauth_provider)
    {
      $data = [
  			'email'=>$username,
  			'oauth_provider'=>$oauth_provider,
  		];
      $record = BoltUsers::find()
        ->where($data)
        ->one();

      if($record===null){
    		return null;
    	}	else {
        $data = [
    			'oauth_uid'=>$record->oauth_uid,
    			'oauth_provider'=>$oauth_provider,
    		];
        $social = BoltSocialUsers::find()
          ->where($data)
          ->one();

				// fix per salvare un social user nel caso non fosse stato salvato
				if (null === $social){
					//DEVE ESSERE IDENTICO COME site/signup!!
					// $social = new Socialusers;
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

				$array = array(
					'id_user' => $record->id_user,
					'name' => $social->first_name,
					'surname' => $social->last_name,
					'email' => $username,
					'username' => $social->username,
					'picture' => $social->picture,
					'provider'=> $social->oauth_provider,
					'oauth_uid'=> $record->oauth_uid,
					'facade' => 'dashboard',
				);

        Yii::$app->session->set('objUser',$array);

        return new static($record);
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
        // return $this->password === $password;
    }
}
