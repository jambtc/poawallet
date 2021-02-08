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
use yii\helpers\Json;
use yii\helpers\Url;

use Web3\Web3;
use Web3\Contract;
use Web3p\EthereumTx\Transaction;
use Nullix\CryptoJsAes\CryptoJsAes;



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
							'generateTransaction',

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
		$dataProvider->sort->defaultOrder = ['invoice_timestamp' => SORT_DESC];
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
	 * List send page
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
        	return $this->redirect(['/wallet/index']);
    	}

 		return $this->render('send', [
 			'fromAddress' => $fromAddress,
			'sendTokenForm' => $formModel,
			'balance' => $this->Balance($fromAddress),
 		]);
 	}

	/**
	 * List send page
	 */
	public function actionGenerateTransaction()
 	{
		$webapp = new \webapp;

		$fromAccount = $_POST['from'];
 		$toAccount = $_POST['to'];
 		$amount = $_POST['amount'];
		$memo = $_POST['memo'];
		$encrypted = $_POST['prv_key'];
		$passphrase = $webapp->decrypt($_POST['prv_pas']);
		$decrypted = CryptoJsAes::decrypt($encrypted, $passphrase);
		if (null === $decrypted){
			throw new HttpException(404,'Cannot decrypt private key.');
		}

		$settings = \settings::load();
		$this->setDecimals($settings->poa_decimals);
		$amountForContract = $amount * pow(10, $this->getDecimals());

		//CREO la transazione
		/**
		  * This is fairly straightforward as per the ABI spec
		  * First you need the function selector for test(address,uint256) which is the first four bytes of the keccak-256 hash of that string, namely 0xba14d606.
		  * Then you need the address as a 32-byte word: 0x000000000000000000000000c5622be5861b7200cbace14e28b98c4ab77bd9b4.
		  * Finally you need amount (10000) as a 32-byte word: 0x0000000000000000000000000000000000000000000000000000000000002710
			*	0x03746bfdeacebf4f37e099511c16683df3bac8eb																										 0000000000000000000000000000000000000000000000000000000000000079
		*/
		$data_tx = [
			'selector' => '0xa9059cbb', //ERC20	0xa9059cbb function transfer(address,uint256)
			'address' => self::Encode("address", $toAccount), // $receiving_address è l'indirizzo destinatario,
			'amount' => self::Encode("uint", $amountForContract), //$amount l'ammontare della transazione (da moltiplicare per 10^2)
		];

		$poaNode = $webapp->getPoaNode();
		if (!$poaNode)
			throw new HttpException(404,'All Nodes are down...');

		// cerco il nonce
		$web3 = new Web3($poaNode);
		$web3->eth->getTransactionCount($fromAccount, function ($err, $nonce)  {
			if($err !== null) {
				throw new HttpException(404,$err->getMessage());
			}
			$this->setNonce(gmp_intval($nonce->value));
		});

		// imposto il valore del nonce attuale
		$nonce = $this->getNonce();

		// genero la transazione nell'intervallo del nonce
		while ($nonce < 1000)
		{
			$transaction = new Transaction([
			  	'nonce' => '0x'.dechex($nonce), //è un object BigInteger
				'from' => $fromAccount, //indirizzo commerciante
				'to' => $settings->poa_contractAddress, //indirizzo contratto
				'gas' => '0x200b20', // $gas se supera l'importo 0x200b20 va in eerrore gas exceed limit !!!!!!
				'gasPrice' => '1000', // gasPrice giusto?
				'value' => '0',
				'chainId' => $settings->poa_chainId,
				'data' =>  $data_tx['selector'] . $data_tx['address'] . $data_tx['amount'],
			]);

			$transaction->offsetSet('chainId', $settings->poa_chainId);
			// echo '<pre>Transazione: '.print_r($transaction,true).'</pre>';
			// exit;

			$signed_transaction = $transaction->sign($decrypted); // la chiave derivata da json js AES to PHP
			// echo '<pre>Transazione firmata: '.print_r($signed_transaction,true).'</pre>';
			// exit;
			$web3->eth->sendRawTransaction(sprintf('0x%s', $signed_transaction), function ($err, $tx) {
				if ($err !== null) {
					$jsonBody = $this->getJsonBody($err->getMessage());

					// echo '<pre>[response] '.var_dump($jsonBody,true).'</pre>';
					// exit;
					if ($jsonBody === NULL){
						$this->setNonce($this->getNonce() +1);
					}else{
						throw new HttpException(404,$jsonBody['error']['message']);
					}
				}
				$this->setTransaction($tx);

			});
			if ($this->getTransaction() !== null){
				break;
			}
		}
		if ($this->getTransaction() === null){
			throw new HttpException(404,'Invalid nonce: '.$this->getNonce());
		}
		//salva la transazione ERC20 in archivio
		$timestamp = time();
		$invoice_timestamp = $timestamp;

		//calcolo expiration time
		$totalseconds = $settings->poa_expiration * 60; //poa_expiration è in minuti, * 60 lo trasforma in secondi
		$expiration_timestamp = $timestamp + $totalseconds; //DEFAULT = 15 MINUTES

		//$rate = $this->getFiatRate(); // al momento il token è peggato 1/1 sull'euro
		$rate = 1; //eth::getFiatRate('token'); //

		$transaction = new BoltTokens;
		$transaction->id_user = Yii::$app->user->identity->id;
		$transaction->status = 'new';
		$transaction->type = 'token';
		$transaction->token_price = $amount;
		$transaction->token_ricevuti = 0;
		$transaction->fiat_price = abs($rate * $amount);
		$transaction->currency = 'EUR';
		$transaction->item_desc = 'wallet';
		$transaction->item_code = '0';
		$transaction->invoice_timestamp = $invoice_timestamp;
		$transaction->expiration_timestamp = $expiration_timestamp;
		$transaction->rate = $rate;
		$transaction->from_address = $fromAccount;
		$transaction->to_address = $toAccount;
		$transaction->blocknumber = $this->Blocknumber();
		$transaction->txhash = $this->getTransaction();
		$transaction->memo = $memo;

		if (!($transaction->save())){
			throw new HttpException(404,$transaction->errors);
		}

		/** TODO:
		 * 1. salva la notifica
		 * 2. invia messaggio push della notifica
		 * 3. eseguo lo script che si occuperà in background di verificare lo stato dell'invoice appena creata...
		*/

		//adesso posso uscire CON IL JSON DA REGISTRARE NEL SW.
		$data = [
			'id' => $invoice_timestamp, //NECESSARIO PER IL SALVATAGGIO IN  indexedDB quando ritorna al Service Worker
			'id_token' => $webapp->encrypt($transaction->id_token),
			'data'	=> $invoice_timestamp,
			'status' => $transaction->status,
			'token_price' => $transaction->token_price,
			'from_address' => $fromAccount,
			'to_address' => $toAccount,
			'url' => Url::to(['/transactions/view' ,'id'=>$webapp->encrypt($transaction->id_token)]),
		];

		return $this->json($data);
 	}

	/* funzione per codificare il valore $value del tipo $type in hex */
	private function Encode(string $type, $value): string {
		 $len = preg_replace('/[^0-9]/', '', $type);

		 if (!$len) {
			 $len = null;
		 }

		 $type = preg_replace('/[^a-z]/', '', $type);
		 switch ($type) {
			 case "hash":
			 case "address":
				 if (substr($value, 0, 2) === "0x") {
					 $value = substr($value, 2);
				 }
				 break;
			 case "uint":
			 case "int":
				 //$value = BcMath::DecHex($value);
				 $value = dechex($value);
				 break;
			 case "bool":
				 $value = $value === true ? 1 : 0;
				 break;
			 case "string":
				 $value = self::Str2Hex($value);
				 break;
			 default:
				 echo 'Cannot encode value of type '. $type;
				 break;
		 }
		 return substr(str_pad(strval($value), 64, "0", STR_PAD_LEFT), 0, 64);
	 }




	/*
	 * This function generate a transaction
	 */
	// private function generateEmptyTransaction($fields)
	// {
	// 	$fields = (object) $fields;
	//
	// 	$settings = \settings::load();
	//
	// 	//salva la transazione ERC20
	// 	$timestamp = time();
	// 	$invoice_timestamp = $timestamp;
	//
	// 	//calcolo expiration time
	// 	$totalseconds = $settings->poa_expiration * 60; //poa_expiration è in minuti, * 60 lo trasforma in secondi
	// 	$expiration_timestamp = $timestamp + $totalseconds; //DEFAULT = 15 MINUTES
	//
	// 	//$rate = $this->getFiatRate(); // al momento il token è peggato 1/1 sull'euro
	// 	$rate = 1; //eth::getFiatRate('token'); //
	//
	// 	$transaction = new BoltTokens;
	// 	$transaction->id_user = Yii::$app->user->identity->id;
	// 	$transaction->status = 'new';
	// 	$transaction->type = 'token';
	// 	$transaction->token_price = $fields->amount;
	// 	$transaction->token_ricevuti = 0;
	// 	$transaction->fiat_price = abs($rate * $fields->amount);
	// 	$transaction->currency = 'EUR';
	// 	$transaction->item_desc = 'wallet';
	// 	$transaction->item_code = '0';
	// 	$transaction->invoice_timestamp = $invoice_timestamp;
	// 	$transaction->expiration_timestamp = $expiration_timestamp;
	// 	$transaction->rate = $rate;
	// 	$transaction->from_address = $fields->from;
	// 	$transaction->to_address = $fields->to;
	// 	$transaction->blocknumber = $this->Blocknumber();
	// 	$transaction->txhash = '0x0';
	// 	$transaction->memo = $fields->memo;
	//
	// 	if (!($transaction->save())){
	// 		echo var_dump($transaction->errors);
	// 		exit;
	// 	}
	// }

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
