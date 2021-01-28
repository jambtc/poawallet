<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

use yii\base\Model;
use yii\db\ActiveRecord;

use app\models\BoltUsers;
use app\models\BoltSocialusers;
use app\models\LoginForm;

use yii\helpers\Json;


Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
Yii::$classMap['telegram'] = Yii::getAlias('@packages').'/OAuth/oauth-telegram/telegram.php';

class OauthtelegramController extends Controller
{
	public $bot_token;
	public $bot_username;

	public function actionCheckAuthorization()
  {
		$bot_token = \settings::load()->telegramToken; // place bot token of your bot here
		$bot_username = \settings::load()->telegramBotName; // place username of your bot here

		$login = new \telegram($bot_username,$bot_token);
		$auth_data = $login->checkTelegramAuthorization($_GET);

		// FIX CAMBIO USERNAME IN TELEGRAM
		$auth_data['email'] = $auth_data['id'] .'@telegram.com';
		$auth_data['oauth_provider'] = 'telegram';

		// echo "<pre>".print_r($auth_data,true)."</pre>";
		// exit;

		$this->saveUserData($auth_data);
		//
		$model=new LoginForm;
		$model->username = $auth_data['email'];
		$model->password = $auth_data['id'];
		$model->oauth_provider = 'telegram';
		//

		if ($model->validate() && $model->login()) {
				return $this->redirect(['site/dash']);
		}
		return $this->goHome();

  }


	private function saveUserData($auth_data)
	{
		$model = BoltUsers::find()
    ->where([
			'oauth_provider'=>'telegram',
			'oauth_uid'=>$auth_data['id'],
		])
    ->one();



		if (null === $model){
			$model = new BoltUsers();
			$model->username = $auth_data['email'];
			$model->password = $auth_data['id'];
			$model->ga_secret_key = null;
			$model->activation_code = '0';
			$model->status_activation_code = 1;
			$model->oauth_provider = 'telegram';
			$model->oauth_uid = $auth_data['id'];

			if ($model->save()){
				$social = new BoltSocialusers();
				$social->oauth_provider = 'telegram';
				$social->oauth_uid = $auth_data['id'];
				$social->id_user = $model->id_user;
				$social->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
				$social->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
				$social->username = (isset($auth_data['username']) ? $auth_data['username'] : '');
				$social->email = $auth_data['email'];
				$social->picture = (isset($auth_data['photo_url']) ? $auth_data['photo_url'] : '');
				if (!($social->save())){
					echo "<pre>".print_r($social,true)."</pre>";
					exit;
				}
			}else{
				echo "<pre>".print_r($model,true)."</pre>";
				exit;
			}
		}else{
			// $social = Socialusers::model()->findByAttributes(['id_user'=>$model->id_user]);
			$social = BoltSocialusers::find()
	    ->where(['id_user'=>$model->id])
	    ->one();
			// $social = new Socialusers;
			// $social->load(['id_user'=>$model->id_user]);
			if (null === $social){
				$social = new BoltSocialusers();
			}

			$social->oauth_provider = 'telegram';
			$social->oauth_uid = $auth_data['id'];
			$social->id_user = $model->id;
			$social->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
			$social->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
			$social->username = (isset($auth_data['username']) ? $auth_data['username'] : '');
			$social->email = $auth_data['email'];
			$social->picture = (isset($auth_data['photo_url']) ? $auth_data['photo_url'] : '');
			if (!($social->save())){
				echo "<pre>".print_r($social,true)."</pre>";
				exit;
			}
		}

		$auth_data_json = Json::encode($auth_data);
	  setcookie('tg_user', $auth_data_json);
	}
}
