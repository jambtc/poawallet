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
use app\models\BoltTokensSearch;
use app\models\BoltWallets;
use app\models\SendTokenForm;
use app\models\WizardWalletForm;

use yii\bootstrap4\ActiveForm;

use Web3\Web3;
use Web3\Contract;
use Blocktrail\CryptoJSAES\CryptoJSAES;



Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

class WalletController extends Controller
{
	public $balance = 0; // token balance
	public $decimals = 0; // decimals into smart contract
	public $count = 0; // nonce count

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
	private function setNonce($count){
		$this->count = $count;
	}
	private function getNonce(){
		return $this->count;
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
					'saveSubscription','index', 'qr','send','wizard',
					'restore',
				],
				'rules' => [
					[
						'allow' => true,
						'actions' => ['saveSubscription'],
						'roles' => ['?'],
					],
					[
						'allow' => true,
						'actions' => [
							'index',
							'receive',
							'qr',
							'send',
							'wizard',
							'restore',
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
		// $settings = \settings::loadUser(Yii::$app->user->id);
 		// if (empty($settings->id_wallet)){
 		// 	$fromAddress = '0x0000000000000000000000000000000000000000';
 		// }else{
 		// 	$wallet = BoltWallets::find()
 	    // 		->andWhere(['id_wallet'=>$settings->id_wallet])
 	    // 		->one();
		//
 		// 	$fromAddress = $wallet->wallet_address;
 		// }
		// return $fromAddress;
		// $settings = \settings::loadUser(Yii::$app->user->id);
 		// if (empty($settings->id_wallet)){
 		// 	$fromAddress = '0x0000000000000000000000000000000000000000';
 		// }else{
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
	 * Lists wallet dashboard page
	 */
	public function actionIndex()
	{
		$fromAddress = $this->userAddress();

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
				'balance' => $this->Balance($fromAddress),
		]);
	}

	/**
	 * List receive page
	 */
	public function actionReceive()
 	{
		$fromAddress = $this->userAddress();

 		return $this->render('receive', [
 			'fromAddress' => $fromAddress,
 		]);
 	}

	/**
	 * List receive page
	 */
	public function actionSend()
 	{
		// echo '<pre>'.print_r($_POST,true).'</pre>';
		// exit;
 		$fromAddress = $this->userAddress();

		$formModel = new SendTokenForm; //form di input dei dati

		if (Yii::$app->request->isAjax && $formModel->load(Yii::$app->request->post())) {
		    Yii::$app->response->format = Response::FORMAT_JSON;
			// echo '<pre>'.print_r(ActiveForm::validate($sendTokenForm),true).'</pre>';
		    return ActiveForm::validate($formModel);
		}

		if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
			$this->generateTransaction($formModel);
        	return $this->redirect(['/wallet/index']);
    	}

 		return $this->render('send', [
 			'fromAddress' => $fromAddress,
			'sendTokenForm' => $formModel,
			'balance' => $this->Balance($fromAddress),
 		]);
 	}
	/*
	 * This function generate a transaction
	 */
	private function generateTransaction($fields)
	{
		$pow = 0.00021 * pow(10,10);
		$hex = dechex($pow);
		$gas = '0x'.$hex;

		$prv_key = CryptoJSAES::decrypt($fields->prv_pas, $fields->prv_key);

		if (null === $prv_key){
			throw new HttpException(404,'PRivate key not found.');
		}
		echo '<pre>$prv_key: '.print_r($prv_key,true).'</pre>';
		echo '<pre>pow: '.print_r($pow,true).'</pre>';
		echo '<pre>gas: '.print_r($gas,true).'</pre>';
		exit;
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


	/**
	 * Show wizard generation first wallet address page
	 */
	public function actionWizard()
 	{
		$this->layout = 'wizard';
 		return $this->render('wizard');
 	}

	/**
	 * Show Restore old wallet page
	 */
	public function actionRestore()
 	{
		$this->layout = 'wizard';

		$formModel = new WizardWalletForm; //form di input dei dati

		if (Yii::$app->request->isAjax && $formModel->load(Yii::$app->request->post())) {
		    Yii::$app->response->format = Response::FORMAT_JSON;
			// echo '<pre>'.print_r(ActiveForm::validate($sendTokenForm),true).'</pre>';
		    return ActiveForm::validate($formModel);
		}

		if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
			// salvo l'indirizzo in tabella
			$boltWallet = new BoltWallets;
			$boltWallet->id_user = Yii::$app->user->identity->id;
			$boltWallet->wallet_address = Yii::$app->request->post('WizardWalletForm')['address'];
			$boltWallet->blocknumber = '0x0';

			if ($boltWallet->save())
        		return $this->redirect(['/wallet/index']);
			else
				var_dump( $boltWallet->getErrors());

			exit;
    	}

 		return $this->render('restore', [
			'formModel' => $formModel,
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
