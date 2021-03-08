<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\Users;
use app\models\LoginForm;
use yii\helpers\Json;

use jambtc\oauthtelegram;

use app\components\Settings;


// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
// Yii::$classMap['telegram'] = Yii::getAlias('@packages').'/OAuth/oauth-telegram/telegram.php';

class OauthtelegramController extends Controller
{
	public $bot_token;
	public $bot_username;

	public function actionCheckAuthorization()
  {
		$bot_token = Settings::load()->telegramToken; // place bot token of your bot here
		$bot_username = Settings::load()->telegramBotName; // place username of your bot here

		$login = new \jambtc\oauthtelegram\telegram($bot_username,$bot_token);
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
		$model->oauth_provider = $auth_data['oauth_provider'];
		//

		if ($model->validate() && $model->login()) {
				return $this->redirect(['wallet/index']);
		}
		return $this->goHome();

  }


	private function saveUserData($auth_data)
	{
		$model = Users::find()
    	->where([
			'oauth_provider'=>$auth_data['oauth_provider'],
			'oauth_uid'=>$auth_data['id'],
		])
    	->one();

		if (null === $model){
			$model = new Users();
			$model->username = $auth_data['email'];
			$model->password = $auth_data['id'];
			$model->ga_secret_key = null;
			$model->activation_code = '0';
			$model->status_activation_code = 1;
			$model->oauth_provider = $auth_data['oauth_provider'];
			$model->oauth_uid = $auth_data['id'];
			$model->authKey = Yii::$app->security->generateRandomString();
			$model->accessToken = Yii::$app->getSecurity()->generatePasswordHash($model->getAuthKey());

			$model->email = $auth_data['email'];
            $model->facade = 'dashboard';
			$model->provider = $auth_data['oauth_provider'];
			$model->picture = (isset($auth_data['photo_url']) ? $auth_data['photo_url'] : 'css/images/anonymous.png');
			$model->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
			$model->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
			$model->save();
		}

		$auth_data_json = Json::encode($auth_data);
		setcookie('tg_user', $auth_data_json);
	}
}
