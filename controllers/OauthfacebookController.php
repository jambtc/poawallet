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

use jambtc\oauthfacebook;

// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';

class OauthfacebookController extends Controller
{


	public function actionCheckAuthorization()
  {
		$auth_data = $_POST;
		$auth_data['oauth_provider'] = 'facebook';

		$this->saveUserData($auth_data);
		//
		$model=new LoginForm;
		$model->username = $auth_data['id'] .'@facebook.com'; // fix email change fb
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
		$model = BoltUsers::find()
    ->where([
			'oauth_provider'=>$auth_data['oauth_provider'],
			'oauth_uid'=>$auth_data['id'],
		])
    ->one();

		if (null === $model){
			$model = new BoltUsers();

			$model->username = $auth_data['id'] .'@facebook.com'; //$auth_data['email'];
			$model->password = $auth_data['id'];
			$model->ga_secret_key = null;
			$model->activation_code = '0';
			$model->status_activation_code = 1;
			$model->oauth_provider = $auth_data['oauth_provider'];
			$model->oauth_uid = $auth_data['id'];
			$model->authKey = Yii::$app->security->generateRandomString();
			$model->accessToken = Yii::$app->getSecurity()->generatePasswordHash($model->getAuthKey());

			if ($model->save()){
				$social = new BoltSocialusers();

				$social->oauth_provider = $auth_data['oauth_provider'];
				$social->oauth_uid = $auth_data['id'];
				$social->id_user = $model->id;
				$social->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
				$social->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
				$social->username = (isset($auth_data['username']) ? $auth_data['username'] : '');
				$social->email = $auth_data['email'];
				$social->picture = 'https://graph.facebook.com/'. $auth_data['id'] .'/picture';

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
			// $social->picture = (isset($auth_data['picture']) ? $auth_data['picture'] : 'css/images/anonymous.png');
			$social->picture = 'https://graph.facebook.com/'. $auth_data['id'] .'/picture';
			if (!($social->save())){
				echo "<pre>".print_r($social,true)."</pre>";
				exit;
			}
		}
		// FIX CAMBIO mail IN FB
		$auth_data['email'] = $auth_data['id'] .'@facebook.com'; //$auth_data['email'];

		$auth_data_json = Json::encode($auth_data);
	}
}
