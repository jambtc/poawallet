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
use app\models\BoltTokens;


use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\Html;

// use Web3\Web3;
// use Web3\Contract;


Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
// // Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';


class BlockchainController extends Controller
{
	public $maxBlocksToScan = 10; // 200 IS FOR TESTING AT HOME 1500; //don't touch it (1500)
	public $transactionsFound = [];

	private function setMaxBlocksToScan($maxBlocksToScan){
		$this->maxBlocksToScan = $maxBlocksToScan;
	}
	private function getMaxBlocksToScan(){
		return $this->maxBlocksToScan;
	}
	private function setTransactionsFound($transaction){
		$this->transactionsFound["id"] = time();
		$this->transactionsFound["success"] = true;
		$this->transactionsFound["openUrl"] = "index.php?r=tokens/index";
		$this->transactionsFound["transactions"][] = $transaction;
	}
	private function getTransactionsFound(){
		return $this->transactionsFound;
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

	// verifica eventuali transazioni dell'utente a partire dal getBlockNumber
	// dell'utente fino ad arrivare all'ultimo blocco della blockchain
	public function actionCheckTransactions()
	{
		// echo '<pre>'.print_r($_POST,true).'</pre>';
		// exit;
		// set_time_limit(0); //imposto il time limit unlimited

		$chainBlock = $_POST['chainBlocknumber'];

		// $filename = Yii::app()->basePath."/log/blockchain-search.log";
		// $myfile = fopen($filename, "a");

		//carico info del wallet
		$wallets = BoltWallets::find()
		   ->andWhere(['id_user'=>Yii::$app->user->id])
		   ->one();

		$savedBlock = $wallets->blocknumber; //dovrebbe essere già stato salvato in formato hex
		$SEARCH_ADDRESS = strtoupper($_POST['search_address']);

		//Carico i parametri della webapp
		$settings = \settings::load();

		// numero massimo di blocchi da scansionare
		// if (isset($settings->maxBlocksToScan))
		// 	$this->setMaxBlocksToScan($settings->maxBlocksToScan);

		// Inizio il ciclo sui blocchi
		for ($x=0; $x<$this->getMaxBlocksToScan();$x++)
		{
			if ((hexdec($savedBlock)+$x) <= hexdec($chainBlock)){
				//somma del valore del blocco in decimali
				$searchBlock = '0x'. dechex (hexdec($savedBlock) + $x );

			   // $eth->getBlockByNumber($searchBlock,true, function ($err, $block){
				//    if ($err !== null) {
				// 	   $save = new Save;
				// 	   $save->WriteLog('bolt','blockchain','SyncBlockchain',"Error while searching blocks.");
				// 	   echo CJSON::encode(array(
				// 		   'success'=>false,
				// 		   'error'=>$err->getMessage()
				// 	   ));
				// 	   exit;
				//    }
				//    self::setTransactions($block->transactions);
			   // });
				$block = Yii::$app->Erc20->getBlockInfo($searchBlock,true);
				$transactions = $block->transactions;
				// echo '<pre>'.print_r($transactions,true).'</pre>';
				// exit;

				if (!empty($transactions))
				{
					foreach ($transactions as $transaction)
					{
						//controlla transazioni ethereum
						if (strtoupper($transaction->to) <> strtoupper($settings->poa_contractAddress) ){
							$ReceivingType = 'ether';
					   }else{
						   //smart contract
						   $ReceivingType = 'token';
						   // $transactionId = $transaction->hash;
						   $transactionContract = Yii::$app->Erc20->getReceipt($transaction->hash);

						   // $contract->eth->getTransactionReceipt($transactionId, function ($err, $receipt) use (&$transactionContract)
						   // {
							//  if ($err !== null) {
							// 	   $save = new Save;
							// 	   $save->WriteLog('bolt','blockchain','SyncBlockchain',"Error while getting transaction receipt.");
							// 	   echo CJSON::encode(array(
							// 		   'success'=>false,
							// 		   'error'=>$err->getMessage()
							// 	   ));
							// 	   exit;
							//    }
				 		   // 		if ($receipt)
							// 	   $transactionContract = $receipt;
						   // });
						   if ($transactionContract <> '' && !(empty($transactionContract->logs)))
						   {
							   $receivingAccount = $transactionContract->logs[0]->topics[2];
							   $receivingAccount = str_replace('000000000000000000000000','',$receivingAccount);

							   // verifica se nella transazione ricevi o hai inviato
							   if (strtoupper($receivingAccount) == $SEARCH_ADDRESS || strtoupper($transactionContract->from) == $SEARCH_ADDRESS){
								   $save = new Save;

								   // carica da db tramite hash (che è univoco)
								   $tokens = BoltTokens::find()->findByHash($transactionContract->transactionHash);

								   // SE da DB è null, è NECESSARIA LA NOTIFICA per chi invia e riceve
								   if (null===$tokens){
									   //$save->WriteLog('bolt','blockchain','SyncBlockchain',"Transaction found but it isn\'t in DB. I\'ll save it in DB.");

									   //salva la transazione
									   $timestamp = 0;
									   $transactionValue = Yii::$app->Erc20->wei2eth(
										   $transactionContract->logs[0]->data,
										   $settings->poa_decimals
									   ); // decimali del token
									   $rate = 1; //eth::getFiatRate('token');

									   // con questa funzione recupero il timestamp della transazione
									   // NB: il timestamp è quello sul server POA.
									   // $eth->getBlockByHash($transaction->blockHash,true, function ($err, $block) use (&$timestamp){
										//    if ($err !== null) {
										// 	   $save->WriteLog('bolt','blockchain','SyncBlockchain',"Error while getting block by hash.");
										// 	   echo CJSON::encode(array(
										// 		   'success'=>false,
										// 		   'error'=>$err->getMessage()
										// 	   ));
										// 	   exit;
										//    }
										//    $timestamp = hexdec($block->timestamp);
									   // });
									   $blockByHash = Yii::$app->Erc20->getBlockByHash($transaction->blockHash);



									   // salvo la transazione NULL in db. Restituisce object
									   $tokens = new BoltTokens;

									   $attributes = array(
										   'id_user' => $wallets->id_user,
										   'status'	=> 'complete',
										   'type'	=> 'token',
										   'token_price'	=> $transactionValue,
										   'token_ricevuti'	=> $transactionValue,
										   'fiat_price'		=>  abs($rate * $transactionValue),
										   'currency'	=> 'EUR',
										   'item_desc' => 'wallet',
										   'item_code' => '0',
										   'invoice_timestamp' => hexdec($blockByHash->timestamp),
										   'expiration_timestamp' => hexdec($blockByHash->timestamp) + 60*15, //15 min. standard
										   'rate' => $rate,
										   'from_address' => $transaction->from,
										   'to_address' => $receivingAccount,
										   'blocknumber' => hexdec($transactionContract->blockNumber),
										   'txhash'	=> $transactionContract->transactionHash,
									   );
									   $tokens->load($attributes);
									   $tokens->save();

									   // $save->WriteLog('bolt','blockchain','SyncBlockchain',"Saving transaction: <pre>".print_r($attributes,true)."</pre>\n");

									   // imposto l'array contenente le transazioni e che sarà restituito alla funzione chiamante
									   // $this->setTransactionFound(array(
										//    'id_token' => crypt::Encrypt($tokens->id_token),
										//    'data' => WebApp::dateLN($tokens->invoice_timestamp,$tokens->id_token),
										//    'status' => WebApp::walletIconStatus($tokens->status),
										//    'token_price' => WebApp::typePrice($transactionValue,(strtoupper($transactionContract->from) == $SEARCH_ADDRESS ? 'sent' : 'received')),
										//    'from_address' => $tokens->from_address,
										//    'to_address' => $tokens->to_address,
										//    'url' => Yii::app()->createUrl("tokens/view",['id'=>crypt::Encrypt($tokens->id_token)]),
										//    'title' => Yii::t('notify','New transaction'),
										//    'message' => Yii::t('notify','A transaction you received has been completed.'),
									   // ));

									   // notifica per chi ha inviato (from_address)
									   // $notification = array(
										//    'type_notification' => 'token',
										//    'id_user' => Wallets::model()->findByAttributes(['wallet_address'=>$tokens->from_address]) === null ? 1 : Wallets::model()->findByAttributes(['wallet_address'=>$tokens->from_address])->id_user,
										//    'id_tocheck' => $tokens->id_token,
										//    'status' => 'complete',
										//    'description' => 'A transaction has been completed.',
										//    'url' => Yii::app()->createUrl("tokens/view",['id'=>crypt::Encrypt($tokens->id_token)]),
										//    'timestamp' => time(),
										//    'price' => $tokens->token_price,
										//    'deleted' => 0,
									   // );
									   // $save->Notification($notification);

									   // notifica per chi riceve (to_address)
									   // $notification = array(
										//    'type_notification' => 'token',
										//    // 'id_user' => Wallets::model()->findByAttributes(['wallet_address'=>$tokens->to_address])->id_user,
										//    'id_user' => Yii::app()->user->objUser['id_user'],
										//    'id_tocheck' => $tokens->id_token,
										//    'status' => 'complete',
										//    'description' => 'A transaction you received has been completed.',
										//    'url' => Yii::app()->createUrl("tokens/view",['id'=>crypt::Encrypt($tokens->id_token)]),
										//    'timestamp' => time(),
										//    'price' => $tokens->token_price,
										//    'deleted' => 0,
									   // );
									   // $save->Notification($notification);
									   // $save->WriteLog('bolt','blockchain','SyncBlockchain',"Notification saved.");
								   }else{
									   // se NON è NULL notifico solo il ricevente
									   if (strtoupper($tokens->to_address) == $SEARCH_ADDRESS){
										   if ($tokens->token_ricevuti == 0 ){ //&& $tokens->status <> 'complete'){
											   $this->setTransactionsFound(array(
												   'id_token' => crypt::Encrypt($tokens->id_token),
												   'data' => WebApp::dateLN($tokens->invoice_timestamp,$tokens->id_token),
												   'status' => WebApp::walletIconStatus($tokens->status),
												   'token_price' => WebApp::typePrice($tokens->token_price,(strtoupper($transaction->from) == $SEARCH_ADDRESS ? 'sent' : 'received')),
												   'from_address' => $tokens->from_address,
												   'to_address' => $tokens->to_address,
												   'url' => Yii::app()->createUrl("tokens/view",['id'=>crypt::Encrypt($tokens->id_token)]),
												   'title' => Yii::t('notify','New transaction'),
												   'message' => Yii::t('notify','A transaction you received has been completed.'),
											   ));
											   $tokens->status = 'complete';
											   $tokens->token_ricevuti = $tokens->token_price;
											   $tokens->update();
											   $save->WriteLog('bolt','blockchain','SyncBlockchain',"Transaction ".crypt::Encrypt($tokens->id_token)." updated.");

											   //salva la notifica
											   $notification = array(
												   'type_notification' => 'token',
												   'id_user' => Yii::app()->user->objUser['id_user'],
												   'id_tocheck' => $tokens->id_token,
												   'status' => 'complete',
												   'description' => 'You received a new transaction.',
												   'url' => Yii::app()->createUrl("tokens/view",['id'=>crypt::Encrypt($tokens->id_token)]),
												   'timestamp' => time(),
												   'price' => $tokens->token_price,
												   'deleted' => 0,
											   );
											   $save->Notification($notification);
										   }
									   }
								   }
								   // in entrambi i casi controllo che il wallet di chi invia sia
								   // di un Istituto, nel qual caso Faccio partire il timer
								   // per il messaggio di alert
								   // $institute = Institutes::model()->findByAttributes(['wallet_address'=>$tokens->from_address]);
								   // if ($institute !== null){
								   // 	// eseguo lo script che si occuperà in background di
								   // 	// inviare il messaggio di alert all'utente
								   // 	$cmd = Yii::app()->basePath.DIRECTORY_SEPARATOR.'yiic alert --iduser='.crypt::Encrypt(Wallets::model()->findByAttributes(['wallet_address'=>$tokens->to_address])->id_user). ' --idInstitute='.crypt::Encrypt($institute->id_institute);
								   // 	Utils::execInBackground($cmd);
								   // }
							   }
						   }
					   } //endif 'ethereum or token'
		   			}
			   }//for loop
			   //aggiorno il numero dei blocchi sul wallet
			   // print_r($searchBlock);
			   $wallets->blocknumber = $searchBlock;
			   $wallets->update();
		   }else{
			   break;
		   }
	   }

	   if (!(empty($this->getTransactionsFound()))){
		   // restituisco le transazioni
		   return $this->json($this->getTransactionsFound());
	   }else{
		   // non ho trovato nulla
		   $return = [
			   'id'=>time(),
			   'success'=>false,
			   'error'=>'',
			   'latestSearchedBlockNumber' => $searchBlock,
		   ];

		   return $this->json($return);
	   }
	}


	// recupera il blocknumber attuale della blockchain
	// recupera il blocknumber del wallet utente
	// mostra la differenza dei blocchi
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

		$block = Yii::$app->Erc20->getBlockInfo();

		//calcolo la differenza tra i blocchi
		$difference = hexdec($block->number) - hexdec($wallet->blocknumber);

		$return = [
			 'id'=>time(),
			 "walletBlocknumber"=>$wallet->blocknumber,
			 "chainBlocknumber"=>$block->number,
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
