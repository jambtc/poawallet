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
use app\models\SendTokenForm;
use app\models\WizardWalletForm;
use app\models\PushSubscriptions;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Url;

use app\components\WebApp;
use Nullix\CryptoJsAes\CryptoJsAes;

use app\components\Settings;


// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

class SendController extends Controller
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
					'generateTransaction',
					'validateTransaction',
				],
				'rules' => [
					[
						'allow' => true,
						'actions' => [
							'index',
							'generateTransaction',
							'validateTransaction'
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
	 * List send page
	 */
	public function actionIndex()
 	{
		// echo '<pre>'.print_r($_POST,true).'</pre>';
		// exit;
 		$fromAddress = BoltWallets::find()->userAddress(Yii::$app->user->id);

		$formModel = new SendTokenForm; //form di input dei dati

		if (Yii::$app->request->isAjax && $formModel->load(Yii::$app->request->post())) {
		    Yii::$app->response->format = Response::FORMAT_JSON;
			// echo '<pre>'.print_r(ActiveForm::validate($sendTokenForm),true).'</pre>';
		    return ActiveForm::validate($formModel);
		}

		if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
        	return $this->redirect(['/wallet/index']);
    	}

 		return $this->render('index', [
 			'fromAddress' => $fromAddress,
			'sendTokenForm' => $formModel,
			'balance' => Yii::$app->Erc20->Balance($fromAddress),
			'userImage' => $this->loadSocialUser()->picture,
 		]);
 	}

	/**
	 * List send page
	 */
	public function actionGenerateTransaction()
 	{
		$WebApp = new WebApp;

		$fromAccount = $_POST['from'];
 		$toAccount = $_POST['to'];
 		$amount = $_POST['amount'];
		$memo = $_POST['memo'];
		$encrypted = $_POST['prv_key'];
		$passphrase = $WebApp->decrypt($_POST['prv_pas']);
		$decrypted = CryptoJsAes::decrypt($encrypted, $passphrase);
		// print_r($decrypted);
		// exit;
		if (null === $decrypted){
			throw new HttpException(404,'Cannot decrypt private key.');
		}

		$settings = Settings::load();
		$amountForContract = $amount * pow(10, $settings->poa_decimals);

		//CREO la transazione
		// /**
		//   * This is fairly straightforward as per the ABI spec
		//   * First you need the function selector for test(address,uint256) which is the first four bytes of the keccak-256 hash of that string, namely 0xba14d606.
		//   * Then you need the address as a 32-byte word: 0x000000000000000000000000c5622be5861b7200cbace14e28b98c4ab77bd9b4.
		//   * Finally you need amount (10000) as a 32-byte word: 0x0000000000000000000000000000000000000000000000000000000000002710
		// 	*	0x03746bfdeacebf4f37e099511c16683df3bac8eb																										 0000000000000000000000000000000000000000000000000000000000000079
		// */
		// $data_tx = [
		// 	'selector' => '0xa9059cbb', //ERC20	0xa9059cbb function transfer(address,uint256)
		// 	'address' => self::Encode("address", $toAccount), // $receiving_address è l'indirizzo destinatario,
		// 	'amount' => self::Encode("uint", $amountForContract), //$amount l'ammontare della transazione (da moltiplicare per 10^2)
		// ];

		// $poaNode = $WebApp->getPoaNode();
		// if (!$poaNode)
		// 	throw new HttpException(404,'All Nodes are down...');

		// cerco il nonce
		// $web3 = new Web3($poaNode);
		// $web3->eth->getTransactionCount($fromAccount, function ($err, $nonce)  {
		// 	if($err !== null) {
		// 		throw new HttpException(404,$err->getMessage());
		// 	}
		// 	$this->setNonce(gmp_intval($nonce->value));
		// });

		// imposto il valore del nonce attuale
		// $nonce = $this->getNonce();
		$block = Yii::$app->Erc20->getBlockInfo();
		$nonce = Yii::$app->Erc20->getNonce($fromAccount);

		// genero la transazione nell'intervallo del nonce
		$maxNonce = $nonce + 10;
		while ($nonce < $maxNonce)
		{
			$tx = Yii::$app->Erc20->SendToken([
				'nonce' => $nonce,
				'from' => $fromAccount, //indirizzo commerciante
				'contractAddress' => $settings->poa_contractAddress, //indirizzo contratto
				'toAccount' => $toAccount,
				'amount' => $amountForContract,
				'gas' => '0x200b20', // $gas se supera l'importo 0x200b20 va in eerrore gas exceed limit !!!!!!
				'gasPrice' => '1000', // gasPrice giusto?
				'value' => '0',
				// 'chainId' => $settings->poa_chainId,
				//'data' =>  $data_tx['selector'] . $data_tx['address'] . $data_tx['amount'],
				'chainId' => $settings->poa_chainId,
				'decryptedSign' => $decrypted,
			]);


			// $transaction = new Transaction([
			//   	'nonce' => '0x'.dechex($nonce), //è un object BigInteger
			// 	'from' => $fromAccount, //indirizzo commerciante
			// 	'to' => $settings->poa_contractAddress, //indirizzo contratto
			// 	'gas' => '0x200b20', // $gas se supera l'importo 0x200b20 va in eerrore gas exceed limit !!!!!!
			// 	'gasPrice' => '1000', // gasPrice giusto?
			// 	'value' => '0',
			// 	'chainId' => $settings->poa_chainId,
			// 	'data' =>  $data_tx['selector'] . $data_tx['address'] . $data_tx['amount'],
			// ]);
			//
			// $transaction->offsetSet('chainId', $settings->poa_chainId);
			// echo '<pre>Transazione: '.print_r($transaction,true).'</pre>';
			// exit;

			// $signed_transaction = $transaction->sign($decrypted); // la chiave derivata da json js AES to PHP
			// echo '<pre>Transazione firmata: '.print_r($signed_transaction,true).'</pre>';
			// exit;
			// $web3->eth->sendRawTransaction(sprintf('0x%s', $signed_transaction), function ($err, $tx) {
			// 	if ($err !== null) {
			// 		$jsonBody = $this->getJsonBody($err->getMessage());
			//
			// 		// echo '<pre>[response] '.var_dump($jsonBody,true).'</pre>';
			// 		// exit;
			// 		if ($jsonBody === NULL){
			// 			$this->setNonce($this->getNonce() +1);
			// 		}else{
			// 			throw new HttpException(404,$jsonBody['error']['message']);
			// 		}
			// 	}
			// 	$this->setTransaction($tx);
			//
			// });
			if ($tx !== null){
				break;
			} else {
				$nonce++;
			}
		}
		// echo '<pre>'.print_r($tx,true).'</pre>';
		// exit;


		if ($tx === null){
			throw new HttpException(404,'Invalid nonce: '.$nonce);
		}

		// // get Blocknumber by transactionHash
		// $receipt = Yii::$app->Erc20->getReceipt($tx);
		// $block = Yii::$app->Erc20->getBlockByHash($tx);
		//
		// echo '<pre>receipt'.print_r($receipt,true).'</pre>';
		// echo '<pre>block'.print_r($block,true).'</pre>';
		// exit;

		//salva la transazione ERC20 in archivio
		$timestamp = time();
		$invoice_timestamp = $timestamp;

		//calcolo expiration time
		$totalseconds = $settings->poa_expiration * 60; //poa_expiration è in minuti, * 60 lo trasforma in secondi
		$expiration_timestamp = $timestamp + $totalseconds; //DEFAULT = 15 MINUTES

		//$rate = $this->getFiatRate(); // al momento il token è peggato 1/1 sull'euro
		$rate = 1; //eth::getFiatRate('token'); //

		$transaction = new BoltTokens;
		$transaction->id_user = Yii::$app->user->id;
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
		$transaction->blocknumber = hexdec($block->number);
		$transaction->txhash = $tx;
		$transaction->memo = $memo;

		if (!($transaction->save())){
			throw new HttpException(404,print_r($transaction->errors));
		}

		/** TODO:
		 * 1. salva la notifica
		 * 2. invia messaggio push della notifica
		 * 3. eseguo lo script che si occuperà in background di verificare lo stato dell'invoice appena creata...
		*/

		//adesso posso uscire CON IL JSON DA REGISTRARE NEL SW.
		$data = [
			'id' => $WebApp->encrypt($transaction->id_token), //NECESSARIO PER IL SALVATAGGIO IN  indexedDB quando ritorna al Service Worker
			'status' => $transaction->status,
			'url' => Url::to(['/send/validate-transaction']),
			'row' => $WebApp->showTransactionRow($transaction,$fromAccount),
		];

		return $this->json($data);
 	}

	// cerca la ricevuta dal transaction hash
	// funzione invocata dal sw

	// testing::
	// curl -X POST -d 'id=S2hNeTVGQTkzWis0ekN3RDV3RVRmdz09' http://localhost/fidpay/web/index.php?r=wallet%2Fvalidate-transaction
	public function actionValidateTransaction()
	{
		set_time_limit(0); //imposto il time limit unlimited
		$maxrequests = 30;
		$requests = 1;

		$WebApp = new WebApp;
		$settings = Settings::load();

		$transaction = BoltTokens::find()
 	     		->andWhere(['id_token'=>$WebApp->decrypt($_POST['id'])])
 	    		->one();

		// $poaNode = $WebApp->getPoaNode();
		// if (!$poaNode)
		// 	throw new HttpException(404,'All Nodes are down...');
		//
		// // cerco il nonce
		// $web3 = new Web3($poaNode);
		// $contract = new Contract($web3->provider, $settings->poa_abi);

		while ($requests < $maxrequests)
		{
			$receipt = Yii::$app->Erc20->getReceipt($transaction->txhash);
			// $contract->eth->getTransactionReceipt($transaction->txhash, function ($err, $tx) {
			// 	if ($err !== null) {
			// 		throw $err;
			// 	}
			// 	if ($tx) {
			// 		$this->setTransaction($tx);
			// 		// echo "\nTransaction has mind:) block number: " . $tx->blockNumber . "\nTransaction dump:\n";
			// 		// var_dump($tx);
			// 		// exit;
			// 	}
			// });
			// $tx = $this->getTransaction();

			if ($receipt !== null){
				break;
			}
			$requests ++;
			sleep(1);

		}
		if ($receipt === null){
			$data = [
				'id' => $_POST['id'], //NECESSARIO PER IL SALVATAGGIO IN  indexedDB quando ritorna al Service Worker
				'status' => $transaction->status,
				'success' => false,
			];

			// throw new HttpException(404,'Transaction is null after '.$requests.' requests.');
		} else {
			$transaction->status = 'complete';
			$transaction->token_ricevuti = $transaction->token_price;
			$transaction->blocknumber = hexdec($receipt->blockNumber);

			if (!($transaction->save())){
				throw new HttpException(404,$transaction->errors);
			}

			//adesso posso uscire CON IL JSON DA REGISTRARE NEL SW.
			$data = [
				'id' => $_POST['id'], //NECESSARIO PER IL SALVATAGGIO IN  indexedDB quando ritorna al Service Worker
				'status' => $transaction->status,
				'success' => true,
				'row' => $WebApp->showTransactionRow($transaction,$transaction->from_address),
				'balance' => Yii::$app->Erc20->Balance($transaction->from_address),
			];

		}

		return $this->json($data);

	}






	private static function json ($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $data;
	}







}
