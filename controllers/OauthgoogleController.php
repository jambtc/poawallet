<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Cookie;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\Users;
use app\models\LoginForm;
use yii\helpers\Json;
use yii\helpers\Url;

use jambtc\oauthgoogle;

// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
// Yii::$classMap['google'] = Yii::getAlias('@packages').'/OAuth/oauth-google/google.php';

class OauthgoogleController extends Controller
{
	public function actionResetCookie()
	{
		setcookie('G_AUTHUSER_LOGOUT','');
	}

	public function actionCheckAuthorization()
  {
		if (isset($_COOKIE['G_AUTHUSER_LOGOUT']) && $_COOKIE['G_AUTHUSER_LOGOUT'] == 'AVOID'){
			$auth_data['success'] = false;
			$auth_data['message'] = 'Google authorization blocked from cookies';

			// $cookie = new Cookie([
			//     'name' => 'G_AUTHUSER_LOGOUT',
			//     'value' => '',
			//     'expire' => time(),
			// ]);
			// \Yii::$app->getResponse()->getCookies()->add($cookie);

			setcookie('G_AUTHUSER_LOGOUT','');
			return Json::encode($auth_data);
			// exit(1);
		}

		$login = new \jambtc\oauthgoogle\google(false);
		$auth_data = $_GET;

		$auth_data['oauth_provider'] = 'google';

		$this->saveUserData($auth_data);
		//
		$model=new LoginForm;
		$model->username = $auth_data['email'];
		$model->password = $auth_data['id'];
		$model->oauth_provider = $auth_data['oauth_provider'];
		//

		// $auth_data['success'] = false;
		// if ($model->validate() && $model->login()) {
		// 	$auth_data['success'] = true;
		// }
		// echo Json::encode($auth_data);

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
			$model->picture = (isset($auth_data['picture']) ? $auth_data['picture'] : 'css/images/anonymous.png');
			$model->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
			$model->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
			$model->save();
		}

		$auth_data_json = Json::encode($auth_data);
	}
}
