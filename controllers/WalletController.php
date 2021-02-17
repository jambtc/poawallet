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

use app\models\BoltTokens;
use app\models\search\BoltTokensSearch;
use app\models\BoltWallets;
use app\models\BoltSocialusers;
// use app\models\SendTokenForm;
// use app\models\WizardWalletForm;
// use app\models\PushSubscriptions;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Url;

use Web3\Web3;
use Web3\Contract;
// use Web3p\EthereumTx\Transaction;
// use Nullix\CryptoJsAes\CryptoJsAes;



Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

class WalletController extends Controller
{
	public $balance = 0; // token balance
	public $decimals = 0; // decimals into smart contract
	public $noncevalue = 0; // nonce count
	public $blocknumber = 0; // blocknumber
	public $transaction = null;

	private function setBalance($balance){
		$value = (string) $balance * 1;
		$this->balance = $value;
	}
	private function getBalance(){
		return $this->balance;
	}
	private function setDecimals($decimals){
		$this->decimals = $decimals;
	}
	private function getDecimals(){
		return $this->decimals;
	}
	private function setNonce($noncevalue){
		$this->noncevalue = $noncevalue;
	}
	private function getNonce(){
		return $this->noncevalue;
	}
	private function setBlocknumber($blocknumber){
		$this->blocknumber = $blocknumber;
	}
	private function getBlocknumber(){
		return $this->blocknumber;
	}
	private function setTransaction($transaction){
		$this->transaction = $transaction;
	}
	private function getTransaction(){
		return $this->transaction;
	}
	//recupera lo streaming json dal contenuto txt del body
	private function getJsonBody($response)
	{
		$start = strpos($response,'{',0);
		$substr = substr($response,$start);
		return json_decode($substr, true);
	}

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


	private function loadSocialUser()
	{
		$user = BoltSocialusers::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->id])
 	    		->one();

		return $user;
	}



	/**
	 * Lists wallet dashboard page
	 */
	public function actionIndex()
	{
		$fromAddress = BoltWallets::find()->userAddress(Yii::$app->user->id);

		$searchModel = new BoltTokensSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->setPagination(['pageSize' => 5]);
		$dataProvider->sort->defaultOrder = ['invoice_timestamp' => SORT_DESC];
		$dataProvider->query
					->orwhere(['=','to_address', $fromAddress])
					->orwhere(['=','from_address', $fromAddress]);

		return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'fromAddress' => $fromAddress,
				'balance' => $this->Balance($fromAddress),
				'userImage' => $this->loadSocialUser()->picture,
		]);
	}





	/*
	* This function retrieve the token balance of an address
	*/
	private function Balance($fromAddress)
	{
		$settings = \settings::load();
		$this->setDecimals($settings->poa_decimals);

		// echo '<pre>'.print_r($settings,true).'</pre>';
		// exit;
		$webapp = new \webapp;
		$poaNode = $webapp->getPoaNode();
		if (!$poaNode)
			throw new HttpException(404,'All Nodes are down...');

		$web3 = new Web3($poaNode);
		$utils = $web3->utils;
		$contract = new Contract($web3->provider, $settings->poa_abi);

		$contract->at($settings->poa_contractAddress)->call('balanceOf', $fromAddress, [
			'from' => $fromAddress
		], function ($err, $result) use ($contract, $utils) {
			if ($err !== null) {
				throw new HttpException(404,$err->getMessage());
			}
			// echo '<pre>'.print_r($result,true).'</pre>';
			// exit;
			if (isset($result)) {
				//$balance = (string) $result[0]->value;
				$value = $utils->fromWei($result[0]->value, 'ether');
				$Value0 = (string) $value[0]->value;
				$Value1 = (float) $value[1]->value / pow(10, $this->getDecimals());

				$this->setbalance($Value0 + $Value1);
			}
			// echo '<pre>'.print_r($this->getBalance(),true).'</pre>';
			// exit;
		});

		return $this->getBalance();

	}

	/*
	* This function retrieve the token balance of an address
	*/
	private function BlockNumber()
	{
		$settings = \settings::load();

		// echo '<pre>'.print_r($settings,true).'</pre>';
		// exit;
		$webapp = new \webapp;
		$poaNode = $webapp->getPoaNode();
		if (!$poaNode)
			throw new HttpException(404,'All Nodes are down...');

		$web3 = new Web3($poaNode);

		// blocco in cui presumibilmente avviene la transazione
		$response = null;
		$web3->eth->getBlockByNumber('latest',false, function ($err, $block) use (&$response){
			if ($err !== null) {
				throw new CHttpException(404,'Errore: '.$err->getMessage());
			}
			$this->setBlocknumber(hexdec($block->number));
		});

		return $this->getBlocknumber();

	}




	private static function json ($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $data;
	}

	public function actionCrypt()
	{
		$data = [
			'cryptedpass' => isset($_POST['pass']) ? \webapp::encrypt($_POST['pass']) : '',
			'cryptedseed' => isset($_POST['seed']) ? \webapp::encrypt($_POST['seed']) : '',
			'cryptediduser' => \webapp::encrypt(Yii::$app->user->id),
		];

		return $this->json($data);
	}

	public function actionDecrypt()
	{
		$data = [
			'decrypted' => isset($_POST['pass']) ? \webapp::decrypt($_POST['pass']) : '',
			'decryptedseed' => isset($_POST['cryptedseed']) ? \webapp::decrypt($_POST['cryptedseed']) : '',
			'decryptediduser' => isset($_POST['cryptediduser']) ? \webapp::decrypt($_POST['cryptediduser']) : '',

		];
		return $this->json($data);
	}






}
