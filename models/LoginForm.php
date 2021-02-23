<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends Model
{
	const TIMEOUT = 7776000; // This number is 60sec * 60min * 24h * 90days

	public $username;
	public $password;
	public $rememberMe = true;

	// provider utilizzato: facebook, google, telegram
	public $oauth_provider;

	private $_user = false;



	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
			return [
					// username and password are both required
					[['username', 'password'], 'required'],
					// rememberMe must be a boolean value
					['rememberMe', 'boolean'],
					// password is validated by authenticate()
					['password', 'authenticate'],

					// username has to be a valid email address
					['username', 'email', 'message'=>Yii::t('lang','Email hasn\'t right format.')],
			];
	}


	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>Yii::t('model','Email'),
			'password'=>Yii::t('model','Password'),
			// 'ga_cod'=>Yii::t('model','Google 2FA'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 * @param string $attribute the name of the attribute to be validated.
	 * @param array $params additional parameters passed with rule when being executed.
	 */
	public function authenticate($attribute,$params)
	{
		if (!$this->hasErrors()) {
				$user = $this->getUser();

				if (!$user || !$user->validatePassword($this->password)) {
						$this->addError($attribute, 'Incorrect username or password.');
				}
		}
	}

	/**
	 * Logs in a user using the provided username and password.
	 * @return bool whether the user is logged in successfully
	 */
	public function login()
	{
			if ($this->validate()) {
				return Yii::$app->user->login($this->getUser(), self::TIMEOUT);
			}
			return false;
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	public function getUser()
	{
			if ($this->_user === false) {
					$this->_user = BoltLogin::findUserByProvider($this->username,$this->oauth_provider);
			}

			return $this->_user;
	}

}
