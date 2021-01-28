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
use yii\helpers\Url;


Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
Yii::$classMap['google'] = Yii::getAlias('@packages').'/OAuth/oauth-google/google.php';

class OauthgoogleController extends Controller
{
	public function actionResetCookies()
	{
		setcookie('G_AUTHUSER_LOGOUT','');
	}

	public function actionCheckAuthorization()
  {
		if (isset($_COOKIE['G_AUTHUSER_LOGOUT']) && $_COOKIE['G_AUTHUSER_LOGOUT'] == 'TRUE'){
			setcookie('G_AUTHUSER_LOGOUT','');
			$auth_data['success'] = false;
			echo Json::encode($auth_data);
			exit(1);
		}

		$login = new \google(false);
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
				return $this->redirect(['site/dash']);
		}
		return $this->goHome();
  }


	private function saveUserData($auth_data)
	{
		$model = BoltUsers::find()
    ->where([
			'oauth_provider'=>$auth_data['oauth_provider'],
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
			$model->oauth_provider = $auth_data['oauth_provider'];
			$model->oauth_uid = $auth_data['id'];
			$model->authKey = Yii::$app->security->generateRandomString();
			$model->accessToken = Yii::$app->getSecurity()->generatePasswordHash($model->getAuthKey());

			// echo "<pre>".print_r($model->attributes,true)."</pre>";
			//  // exit;
			// if ($model->validate()){
			// 	echo "valido";
			// }else{
			// 	echo "non valido";
			// }
			// exit;


			if ($model->save()){
				$social = new BoltSocialusers();

				$social->oauth_provider = $auth_data['oauth_provider'];
				$social->oauth_uid = $auth_data['id'];
				$social->id_user = $model->id;
				$social->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
				$social->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
				$social->username = (isset($auth_data['username']) ? $auth_data['username'] : '');
				$social->email = $auth_data['email'];
				$social->picture = (isset($auth_data['picture']) ? $auth_data['picture'] : 'css/images/anonymous.png');
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

			$social->oauth_provider = $auth_data['oauth_provider'];
			$social->oauth_uid = $auth_data['id'];
			$social->id_user = $model->id;
			$social->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
			$social->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
			$social->username = (isset($auth_data['username']) ? $auth_data['username'] : '');
			$social->email = $auth_data['email'];
			$social->picture = (isset($auth_data['picture']) ? $auth_data['picture'] : 'css/images/anonymous.png');
			if (!($social->save())){
				echo "<pre>".print_r($social,true)."</pre>";
				exit;
			}
		}

		$auth_data_json = Json::encode($auth_data);
	}
}
