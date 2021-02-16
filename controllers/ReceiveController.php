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

use app\models\BoltWallets;



class ReceiveController extends Controller
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
					'index',
				],
				'rules' => [

					[
						'allow' => true,
						'actions' => [
							'index',
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



	/**
	 * This function return the user wallet address
	 */
	 private function userAddress() {
 		$wallet = BoltWallets::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->id])
 	    		->one();

		if (null === $wallet){
			$this->redirect(['wallet/wizard']);
		} else {
			return $wallet->wallet_address;
		}
	}



	/**
	 * List receive page
	 */
	public function actionIndex()
 	{
		$fromAddress = $this->userAddress();

 		return $this->render('index', [
 			'fromAddress' => $fromAddress,
 		]);
 	}




}
