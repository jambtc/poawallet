<?php
namespace app\models;

use yii\base\Model;
use Yii;
use app\models\Users;

/**
 * Model representing Signup Form.
 */
class SignupForm extends Model
{
    public $username;
    // public $email;
    public $password;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            // ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'match',  'not' => true,
                // we do not want to allow users to pick one of spam/bad usernames
                'pattern' => '/\b('.Yii::$app->params['user.spamNames'].')\b/i',
                'message' => Yii::t('app', 'It\'s impossible to have that username.')],
            // ['username', 'unique', 'targetClass' => '\app\models\User',
            //     'message' => Yii::t('app', 'This username has already been taken.')],

            // ['email', 'filter', 'filter' => 'trim'],
            // ['email', 'required'],
            ['username', 'email'],
            ['username', 'string', 'max' => 255],
            // ['email', 'unique', 'targetClass' => '\app\models\User',
            //     'message' => 'This email address has already been taken.'],

            ['username', 'uniqueByProvider'],

            ['password', 'required'],
            // use passwordStrengthRule() method to determine password strength
            // $this->passwordStrengthRule(),

            // // on default scenario, user status is set to active
            // ['status', 'default', 'value' => User::STATUS_ACTIVE, 'on' => 'default'],
            // // status is set to not active on rna (registration needs activation) scenario
            // ['status', 'default', 'value' => User::STATUS_INACTIVE, 'on' => 'rna'],
            // // status has to be integer value in the given range. Check User model.
            // ['status', 'in', 'range' => [User::STATUS_INACTIVE, User::STATUS_ACTIVE]]
        ];
    }

    public function uniqueByProvider()
	{
        if (null !== Users::findUserByProvider($this->username,'mail'))
        {
            $this->addError('email', Yii::t('app','This username has already been taken.'));
            return false;
        }
	}



    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            // 'email' => Yii::t('app', 'Email'),
        ];
    }

    /**
     * Signs up the user.
     * If scenario is set to "rna" (registration needs activation), this means
     * that user need to activate his account using email confirmation method.
     *
     * @return User|null The saved model or null if saving fails.
     */
    public function signup()
    {
        // echo "<pre>".print_r($_POST,true)."</pre>";
		// exit;

        if ($this->validate()) {
            $explodemail = explode('@',$this->username);
    		$explodename = explode('.',$explodemail[0]);

            // set the nonce (it will last 24 h)
            $microtime = explode(' ', microtime());
            $nonce = $microtime[1] . str_pad(substr($microtime[0], 2, 6), 6, '0');

            $randomkey = Yii::$app->security->generateRandomString();
            $secretkey = Yii::$app->security->generateRandomString();

            $user = new Users();
            $user->username = $this->username;
            $user->email = $this->username;
            $user->password = $this->password;
            $user->ga_secret_key = null;
            $user->activation_code = $nonce;
            $user->status_activation_code = 0;
            $user->oauth_provider = 'mail';
            $user->oauth_uid = Yii::$app->security->generateRandomString(16);
            $user->authKey = $secretkey;
            $user->accessToken = $randomkey;
            $user->facade = 'dashboard';
            $user->provider = 'mail';
            $user->picture = 'css/images/anonymous.png';
            $user->last_name = isset($explodename[1]) ? $explodename[1] : '';
            $user->first_name = isset($explodename[0]) ? $explodename[0] : '';

            if ($user->save()){
                $this->sendAccountActivationEmail($user);
                return $user;
            } else {
                return null;
            }

            // if user is saved and role is assigned return user object
            // return $user->save() ? $user : null;
        }
        return false;


    }

    /**
     * Sends email to registered user with account activation link.
     *
     * @param  object $user Registered user.
     * @return bool         Whether the message has been sent successfully.
     */
    public function sendAccountActivationEmail($user)
    {
        return Yii::$app->mailer->compose('accountActivationToken', ['user' => $user])
                                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                                ->setTo($user->email)
                                ->setSubject('Account activation for ' . Yii::$app->name)
                                ->send();
    }
}
