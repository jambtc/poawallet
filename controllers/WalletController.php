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

use app\models\search\TransactionsSearch;
use app\models\MPWallets;
use app\models\Users;
use app\models\Nodes;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Url;

use app\components\WebApp;

class WalletController extends Controller
{


	public function beforeAction($action)
	{
		Yii::$app->user->authTimeout = Yii::$app->params['user.rememberMeDuration'];
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
				'layout' => 'auth',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}

	// public function actionError(){
    //     $this->layout = 'auth';
    //     return $this->render('error');
    // }


	private function loadSocialUser()
	{
		$user = Users::find()
 	     		->andWhere(['id'=>Yii::$app->user->id])
 	    		->one();

		return $user;
	}

	/**
	 * @param POST string address the Ethereum Address to be rescanned
	 */
	public function actionRescan(){
        //azzero il numero dei blocchi dell'indirizzo

		$model = MPWallets::find()->byUserId(Yii::$app->user->id)->one();
		$model->blocknumber = '0x0';
		//$model->save();

		return $this->json([
			'success' => $model->save(),
		]);
	}



	/**
	 * Lists wallet dashboard page
	 */
	public function actionIndex()
	{
		$fromAddress = MPWallets::find()->userAddress(Yii::$app->user->id);
		$node = Nodes::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->id])
 	    		->one();

		if (NULL === $fromAddress || NULL === $node){
			$session = Yii::$app->session;
			$string = Yii::$app->security->generateRandomString(32);
			$session->set('token-wizard', $string );

			return $this->redirect(['wizard/index','token' => $string]);
		}

		$searchModel = new TransactionsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->setPagination(['pageSize' => 5]);
		$dataProvider->sort->defaultOrder = ['invoice_timestamp' => SORT_DESC];
		$dataProvider->query
					->orwhere(['=','to_address', $fromAddress])
					->orwhere(['=','from_address', $fromAddress]);

        $dataProvider->query->andwhere(['=','id_smart_contract', $node->id_smart_contract]);

		return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'fromAddress' => $fromAddress,
				'balance' => Yii::$app->Erc20->Balance($fromAddress),
				'userImage' => $this->loadSocialUser()->picture,
				'balance_gas' => Yii::$app->Erc20->BalanceGas($fromAddress),
				'node' => $node,
		]);
	}


	private static function json ($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $data;
	}

	public function actionCrypt()
	{
		$data = [
			'cryptedpass' => isset($_POST['pass']) ? WebApp::encrypt($_POST['pass']) : '',
			'cryptedseed' => isset($_POST['seed']) ? WebApp::encrypt($_POST['seed']) : '',
			'cryptediduser' => WebApp::encrypt(Yii::$app->user->id),
		];

		return $this->json($data);
	}

	public function actionDecrypt()
	{
		$data = [
			'decrypted' => isset($_POST['pass']) ? WebApp::decrypt($_POST['pass']) : '',
			'decryptedseed' => isset($_POST['cryptedseed']) ? WebApp::decrypt($_POST['cryptedseed']) : '',
			'decryptediduser' => isset($_POST['cryptediduser']) ? WebApp::decrypt($_POST['cryptediduser']) : '',

		];
		return $this->json($data);
	}






}
