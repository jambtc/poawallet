<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

use app\models\Users;
use app\models\UsersSearch;


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
		// $model=new LoginForm;
		// $model->username = $auth_data['email'];
		// $model->password = $auth_data['id'];
		// $model->oauth_provider = 'telegram';
		//
		// if($model->validate() && $model->login())
		// 	$this->redirect(array('site/dash'));
		// else
		//   $this->redirect(array('site/login'));





  }



}
// namespace app\controllers;
//
// use Yii;
// // use app\models\BoltTokens;
// // use app\models\BoltTokensSearch;
// use yii\web\Controller;
// use yii\web\NotFoundHttpException;
// use yii\filters\VerbFilter;

// Yii::$classMap['telegram'] = Yii::getAlias('@packages').'/OAuth/oauth-telegram/telegram.php';

// Yii::import('libs.crypt.crypt');
// Yii::import('libs.NaPacks.Settings');
// Yii::import('libs.NaPacks.SaveModels');
// Yii::import('libs.NaPacks.Save');

// require_once Yii::app()->params['libsPath'] . '/OAuth/oauth-telegram/login.php';

// class Oauth-telegramController extends Controller
// {
// 	/**
// 	 * {@inheritdoc}
// 	 */
// 	public function behaviors()
// 	{
// 			return [
// 					'verbs' => [
// 							'class' => VerbFilter::className(),
// 							'actions' => [
// 									'delete' => ['POST'],
// 							],
// 					],
// 			];
// 	}
// }

	// public function init()
	// {



	/**
	 * @return array action filters
	 */
	// public function filters()
	// {
	// 	return array(
	// 		'accessControl', // perform access control for CRUD operations
	// 		'postOnly + delete', // we only allow deletion via POST request
	// 	);
	// }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	// public function accessRules()
	// {
	// 	return array(
	// 		array('allow', // allow user to perform actions
	// 			'actions'=>array(
	// 				'CheckAuthorization', // check telegram authorization
	// 				//'getTelegramUserData',
	//
	// 			),
	// 			'users'=>array('*'), // no login
	// 			//'users'=>array('@'), //logged users
	// 		),
	// 		array('deny',  // deny all users
	// 			'users'=>array('*'),
	// 		),
	// 	);
	// }


	/**
	 * This function check Telegram authorization
	 */

// 	public function actionCheckAuthorization()
// 	{
// 		$login = new telegram(BOT_USERNAME,BOT_TOKEN);
// 		$auth_data = $login->checkTelegramAuthorization($_GET);
//
// 		// FIX CAMBIO USERNAME IN TELEGRAM
// 		$auth_data['email'] = $auth_data['id'] .'@telegram.com';
// 		$auth_data['oauth_provider'] = 'telegram';
//
// 		$this->saveUserData($auth_data);
//
// 		$model=new LoginForm;
// 		$model->username = $auth_data['email'];
// 		$model->password = $auth_data['id'];
// 		$model->oauth_provider = 'telegram';
//
// 		if($model->validate() && $model->login())
// 			$this->redirect(array('site/dash'));
// 		else
// 		  $this->redirect(array('site/login'));
//
// 	}
//
//
	private function saveUserData($auth_data)
	{
		$data = [
			'oauth_provider'=>'telegram',
			'oauth_uid'=>$auth_data['id'],
		];

		$model = new Users();
		$model->load($data);

		if (null === $model){
			$model->email = $auth_data['email'];
			$model->password = $auth_data['id'];
			$model->ga_secret_key = null;
			$model->activation_code = 0;
			$model->status_activation_code = 1;
			$model->oauth_provider = 'telegram';
			$model->oauth_uid = $auth_data['id'];

			if ($model->insert()){
				$social = new Socialusers;
				$social->oauth_provider = 'telegram';
				$social->oauth_uid = $auth_data['id'];
				$social->id_user = $model->id_user;
				$social->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
				$social->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
				$social->username = (isset($auth_data['username']) ? $auth_data['username'] : '');
				$social->email = $auth_data['email'];
				$social->picture = (isset($auth_data['photo_url']) ? $auth_data['photo_url'] : '');
				$social->insert();
			}
		}else{
			$social = Socialusers::model()->findByAttributes(['id_user'=>$model->id_user]);
			if (null === $social){
				$social = new Socialusers;
			}
			$social->oauth_provider = 'telegram';
			$social->oauth_uid = $auth_data['id'];
			$social->id_user = $model->id_user;
			$social->first_name = (isset($auth_data['first_name']) ? $auth_data['first_name'] : '');
			$social->last_name = (isset($auth_data['last_name']) ? $auth_data['last_name'] : '');
			$social->username = (isset($auth_data['username']) ? $auth_data['username'] : '');
			$social->email = $auth_data['email'];
			$social->picture = (isset($auth_data['photo_url']) ? $auth_data['photo_url'] : '');
			$social->save();
		}

		$auth_data_json = CJSON::encode($auth_data);
	  setcookie('tg_user', $auth_data_json);
	}
// }
