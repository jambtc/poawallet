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

					[['oauth_provider'], 'string', 'max' => 100],


					// username has to be a valid email address
					['username', 'email', 'message'=>Yii::t('app','Email hasn\'t right format.')],
			];
	}


	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>Yii::t('app','Email'),
			'password'=>Yii::t('app','Password'),
			// 'ga_cod'=>Yii::t('app','Google 2FA'),
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
			return Yii::$app->user->login($this->_user, Yii::$app->params['user.rememberMeDuration']);
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
		// echo "<pre>".print_r($this->username,true)."</pre>";
		// echo "<pre>".print_r($this->oauth_provider,true)."</pre>";
		// // echo "<pre>".print_r($this->username,true)."</pre>";
		// exit;
		if ($this->_user === false) {
			$this->_user = Users::findUserByProvider($this->username,$this->oauth_provider);
		}

		return $this->_user;
	}

}
