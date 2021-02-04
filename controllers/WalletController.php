<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

use app\models\BoltTokens;
use app\models\BoltTokensSearch;
use app\models\BoltWallets;


Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';

class WalletController extends Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['saveSubscription', 'index', 'qr'],
				'rules' => [
					[
						'allow' => true,
						'actions' => ['saveSubscription'],
						'roles' => ['?'],
					],
					[
						'allow' => true,
						'actions' => ['index','receive', 'qr'],
						'roles' => ['@'],
					],
				],
			],
			];
	}

	/**
	 * {@inheritdoc}
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}


	/**
	 * Lists wallet dashboard page
	 */
	public function actionIndex()
	{
		// carico il wallet selezionato nei settings
		$settings = \settings::loadUser(Yii::$app->user->id);
		if (empty($settings->id_wallet)){
			$fromAddress = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = BoltWallets::find()
	    		->andWhere(['id_wallet'=>$settings->id_wallet])
	    		->one();

			$fromAddress = $wallet->wallet_address;
		}

		$searchModel = new BoltTokensSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->setPagination(['pageSize' => 5]);
		$dataProvider->query
					->orwhere(['=','to_address', $fromAddress])
					->orwhere(['=','from_address', $fromAddress]);

		return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'fromAddress' => $fromAddress,
		]);
	}

	/**
	 * List receive page
	 */

	public function actionReceive()
 	{
 		// carico il wallet selezionato nei settings
 		$settings = \settings::loadUser(Yii::$app->user->id);
 		if (empty($settings->id_wallet)){
 			$fromAddress = '0x0000000000000000000000000000000000000000';
 		}else{
 			$wallet = BoltWallets::find()
 	    		->andWhere(['id_wallet'=>$settings->id_wallet])
 	    		->one();

 			$fromAddress = $wallet->wallet_address;
 		}

 		return $this->render('receive', [
 				'fromAddress' => $fromAddress,
 		]);
 	}






}
