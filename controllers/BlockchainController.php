<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

use app\models\Notifications;
use app\models\NotificationsReaders;
use app\models\query\NotificationsReadersQuery;
use app\models\BoltWallets;


use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\Html;

use Web3\Web3;
// use Web3\Contract;


Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';


class BlockchainController extends Controller
{
	public function beforeAction($action)
	{
    	$this->enableCsrfValidation = false;
    	return parent::beforeAction($action);
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => [
					'getBlockNumber',
				],
				'rules' => [
					// [
					// 	'allow' => true,
					// 	'actions' => ['saveSubscription'],
					// 	'roles' => ['?'],
					// ],
					[
						'allow' => true,
						'actions' => [
							'getBlockNumber',
						],
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

	// aggiorna tutte le notifiche in "letta"
	// update all rows
	public function actionGetBlocknumber(){
		$wallet = BoltWallets::find()
				->andWhere(['id_user'=>Yii::$app->user->id])
				->one();

		$return = [
			 'id'=>time(),
			 "walletBlocknumber"=>0,
			 "chainBlocknumber"=>0,
			 "diff"=>0,
			 "my_address"=>$wallet->wallet_address,
			 "success"=>false,
		];

		$settings = \settings::load();
		$webapp = new \webapp;

		$poaNode = $webapp->getPoaNode();
		if (!$poaNode)
			return $this->json($return);

		$web3 = new Web3($poaNode);

		// blocco in cui presumibilmente avviene la transazione
		$response = null;
		$web3->eth->getBlockByNumber('latest',false, function ($err, $block) use (&$response){
			if ($err !== null) {
				return $this->json($return);
			}
			$response = $block;
		});

		//calcolo la differenza tra i blocchi
		$difference = hexdec($response->number) - hexdec($wallet->blocknumber);



		$return = [
			 'id'=>time(),
			 "walletBlocknumber"=>$wallet->blocknumber,
			 "chainBlocknumber"=>$response->number,
			 "diff"=>$difference,
			 "my_address"=>$wallet->wallet_address,
			 "success"=>true,
		];

		return $this->json($return);

	}






	private static function json ($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $data;
	}







}
