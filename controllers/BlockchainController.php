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

// use app\models\Notifications;
use app\models\BoltWallets;
use app\models\BoltTokens;


use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\Html;

use app\components\Settings;
use app\components\WebApp;
use app\components\Messages;



// use Web3\Web3;
// use Web3\Contract;


// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
// // Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';


class BlockchainController extends Controller
{
	public $maxBlocksToScan = 51; // 200 IS FOR TESTING AT HOME 1500; //don't touch it (1500)
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
		$this->transactionsFound["openUrl"] = Url::to(['/tokens/view','id'=>WebApp::encrypt($transaction['id_token'])]);
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
		set_time_limit(30); //imposto il time limit unlimited
		$chainBlock = $_POST['chainBlocknumber'];

		// $filename = Yii::$app->basePath."/web/assets/blockchain-search.log";
		// $myfile = fopen($filename, "a");

		//carico info del wallet
		$wallets = BoltWallets::find()
		   ->andWhere(['id_user'=>Yii::$app->user->id])
		   ->one();

		$savedBlock = $wallets->blocknumber; //dovrebbe essere già stato salvato in formato hex
		$SEARCH_ADDRESS = strtoupper($_POST['search_address']);

		//Carico i parametri della webapp
		$settings = Settings::load();

		// numero massimo di blocchi da scansionare
		if (isset($settings->maxBlocksToScan))
			$this->setMaxBlocksToScan($settings->maxBlocksToScan);


		$maxBlockToScan = $this->getMaxBlocksToScan();
		// Inizio il ciclo sui blocchi
		for ($x=0; $x < $maxBlockToScan;$x++)
		{
			if ((hexdec($savedBlock)+$x) <= hexdec($chainBlock)){
				//somma del valore del blocco in decimali
				$searchBlock = '0x'. dechex (hexdec($savedBlock) + $x );

			   	// ricerco le informazioni del blocco tramite il suo numero
				$block = Yii::$app->Erc20->getBlockInfo($searchBlock,true);
				$transactions = $block->transactions;
				// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transactions on block n. $searchBlock: \n".'<pre>'.print_r($transactions,true).'</pre>');
				// echo '<pre>'.print_r($transactions,true).'</pre>';
				// exit;

				if (!empty($transactions))
				{
					// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : La transazione non è vuota\n");
					foreach ($transactions as $transaction)
					{
						//controlla transazioni ethereum
						if (strtoupper($transaction->to) <> strtoupper($settings->poa_contractAddress) ){
							// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione ether...\n");
							$ReceivingType = 'ether';
					    }else{
						   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione token...\n");
						   //smart contract
						   $ReceivingType = 'token';
						   // $transactionId = $transaction->hash;
						   // recupero la ricevuta della transazione tramite hash
						   $transactionContract = Yii::$app->Erc20->getReceipt($transaction->hash);

						   if ($transactionContract <> '' && !(empty($transactionContract->logs)))
						   {
							   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione token non vuota...\n");
							   $receivingAccount = $transactionContract->logs[0]->topics[2];
							   $receivingAccount = str_replace('000000000000000000000000','',$receivingAccount);

							   // verifica se nella transazione ricevi o hai inviato
							   if (strtoupper($receivingAccount) == $SEARCH_ADDRESS || strtoupper($transactionContract->from) == $SEARCH_ADDRESS){
								   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione token che appartiene all'utente...\n");
								   // $save = new Save;

								   // carica da db tramite hash (che è univoco)
								   $tokens = BoltTokens::find()->findByHash($transactionContract->transactionHash);

								   // SE da DB è null, è NECESSARIA LA NOTIFICA per chi invia e riceve
								   if (null===$tokens){
									   //$save->WriteLog('bolt','blockchain','SyncBlockchain',"Transaction found but it isn\'t in DB. I\'ll save it in DB.");
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction on blockchain found but it isn\'t in DB. I\'ll save it in DB....\n");

									   //salva la transazione
									   $timestamp = 0;
									   $transactionValue = Yii::$app->Erc20->wei2eth(
										   $transactionContract->logs[0]->data,
										   $settings->poa_decimals
									   ); // decimali del token
									   $rate = 1; //eth::getFiatRate('token');

									   // con questa funzione recupero il timestamp in cui è stata minata
									   // la  transazione
									   // NB: il timestamp è quello sul server POA.

									   $blockByHash = Yii::$app->Erc20->getBlockByHash($transaction->blockHash);

									   // salvo la transazione NULL in db. Restituisce object
									   $tokens = new BoltTokens;
									   $tokens->id_user = $wallets->id_user;
							           $tokens->status	= 'complete';
							           $tokens->type	= 'token';
							           $tokens->token_price	= $transactionValue;
							           $tokens->token_ricevuti	= $transactionValue;
							           $tokens->fiat_price		=  abs($rate * $transactionValue);
							           $tokens->currency	= 'EUR';
							           $tokens->item_desc = 'wallet';
							           $tokens->item_code = '0';
							           $tokens->invoice_timestamp = hexdec($blockByHash->timestamp);
							           $tokens->expiration_timestamp = hexdec($blockByHash->timestamp) + 60*15;
							           $tokens->rate = $rate;
							           $tokens->from_address = $transaction->from;
							           $tokens->to_address = $receivingAccount;
							           $tokens->blocknumber = hexdec($transactionContract->blockNumber);
							           $tokens->txhash = $transactionContract->transactionHash;
									   $tokens->memo = '';
									   $tokens->save();

									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Saving transaction: <pre>".print_r($tokens->attributes,true)."</pre>\n");
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction errors: <pre>".print_r($tokens->errors,true)."</pre>\n");

									   $dateLN = date("d M `y",$tokens->invoice_timestamp);
							           $timeLN = date("H:i:s",$tokens->invoice_timestamp);

									   // imposto l'array contenente le transazioni e che sarà restituito alla funzione chiamante
									   $this->setTransactionsFound([
										   'id_token' => $tokens->id_token,
										   'data' => '<small class="text-muted">'.$dateLN.' <span class="ml-10">'.$timeLN.'</span></small>',
										   'status' => WebApp::Icon($tokens->status),
										   'token_price' => WebApp::typePrice($transactionValue,(strtoupper($transactionContract->from) == $SEARCH_ADDRESS ? 'sent' : 'received')),
										   'from_address' => $tokens->from_address,
										   'to_address' => $tokens->to_address,
										   'url' => Url::to(["/tokens/view",'id'=>WebApp::encrypt($tokens->id_token)]),
										   'title' => Yii::t('lang','New transaction'),
										   'message' => Yii::t('lang','A transaction you received has been completed.'),
									   ]);

									   $id_user_from = BoltWallets::find()->userIdFromAddress($tokens->from_address);

									   // notifica per chi ha inviato (from_address)
									   $notification = [
										   'type_notification' => 'token',
										   'id_user' => $id_user_from,
										   'id_tocheck' => $tokens->id_token,
										   'status' => 'complete',
										   'description' => Yii::t('lang','A transaction you sent has been completed.'),
										   'url' => Url::to(["/tokens/view",'id'=>WebApp::encrypt($tokens->id_token)]),
										   'timestamp' => time(),
										   'price' => $tokens->token_price,
										   'deleted' => 0,
									   ];

									   Messages::push($notification);
									   // $save->Notification($notification);

									   // notifica per chi riceve (to_address)
									   $id_user_to = BoltWallets::find()->userIdFromAddress($tokens->to_address);
									   $notification['id_user'] = $id_user_to === null ? 1 : $id_user_to;
									   $notification['description'] = Yii::t('lang','A transaction you received has been completed.');

									   Messages::push($notification);
									   // $save->Notification($notification);
									   // $save->WriteLog('bolt','blockchain','SyncBlockchain',"Notification saved.");
								   }else{
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction on blockchain found and it is found also on DB....\n");
									   // se NON è NULL notifico solo il ricevente
									   if (strtoupper($tokens->to_address) == $SEARCH_ADDRESS){
										   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : The search address is to_address....\n");

										   if ($tokens->token_ricevuti == 0 ){ //&& $tokens->status <> 'complete'){
											   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Ricevuto è 0....\n");

											   $dateLN = date("d M `y",$tokens->invoice_timestamp);
											   $timeLN = date("H:i:s",$tokens->invoice_timestamp);

											   $this->setTransactionsFound([
												  'id_token' => $tokens->id_token,
												  'data' => '<small class="text-muted">'.$dateLN.' <span class="ml-10">'.$timeLN.'</span></small>',
												  'status' => WebApp::Icon($tokens->status),
												  'token_price' => WebApp::typePrice($tokens->token_price,(strtoupper($transaction->from) == $SEARCH_ADDRESS ? 'sent' : 'received')),
												  'from_address' => $tokens->from_address,
												  'to_address' => $tokens->to_address,
												  'url' => Url::to(["/tokens/view",'id'=>WebApp::encrypt($tokens->id_token)]),
												  'title' => Yii::t('lang','New transaction'),
												  'message' => Yii::t('lang','A transaction you received has been completed.'),
											   ]);
											   $tokens->status = 'complete';
											   $tokens->token_ricevuti = $tokens->token_price;
											   $tokens->update();
											   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Aggiornato tabella tokens....\n");
											   // $save->WriteLog('bolt','blockchain','SyncBlockchain',"Transaction ".crypt::Encrypt($tokens->id_token)." updated.");

											   //salva la notifica
											   $notification = [
												  'type_notification' => 'token',
												  'id_user' => Yii::$app->user->id,
												  'id_tocheck' => $tokens->id_token,
												  'status' => 'complete',
												  'description' => Yii::t('lang','You received a new transaction.'),
												  'url' => Url::to(["/tokens/view",'id'=>WebApp::encrypt($tokens->id_token)]),
												  'timestamp' => time(),
												  'price' => $tokens->token_price,
												  'deleted' => 0,
											  ];

											  Messages::push($notification);
											   // $notification = array(
												//    'type_notification' => 'token',
												//    'id_user' => Yii::app()->user->objUser['id_user'],
												//    'id_tocheck' => $tokens->id_token,
												//    'status' => 'complete',
												//    'description' => 'You received a new transaction.',
												//    'url' => Yii::app()->createUrl("tokens/view",['id'=>crypt::Encrypt($tokens->id_token)]),
												//    'timestamp' => time(),
												//    'price' => $tokens->token_price,
												//    'deleted' => 0,
											   // );
											   // $save->Notification($notification);
										   }
									   }else{
										   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : The search address non è to_address e quindi non faccio nulla....\n");
									   } // notifica al ricevente
								   } // tabella tokens found

							   } // if ricevi o hai inviato
						   }// se transaction contract != ''
					   } //endif 'ethereum or token'
				   } // per ogni transazione trovata
			   }//for loop

			   //aggiorno il numero dei blocchi sul wallet
			   // print_r($searchBlock);
			   $wallets->blocknumber = $searchBlock;
			   $wallets->update();
		   }else{
			   break;
		   }
	   }
	   // // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Searching for transactions. Latest block #: $searchBlock: \n");

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
			 "difference"=>0,
			 "headerMessage"=>'',
			 "my_address"=>$wallet->wallet_address,
			 "relativeTime" =>'',
			 "success"=>false,
		];

		$blockLatest = Yii::$app->Erc20->getBlockInfo();
		$blockWallet = Yii::$app->Erc20->getBlockInfo($wallet->blocknumber);
		// print_r($block);
		// exit;

		//calcolo la differenza tra i blocchi
		$difference = hexdec($blockLatest->number) - hexdec($wallet->blocknumber);

		$return = [
			 'id'=>time(),
			 "walletBlocknumber"=>$wallet->blocknumber,
			 "chainBlocknumber"=>$blockLatest->number,
			 "headerMessage"=> '<small>'.Yii::t('lang', "{n} blocks left.", ['n' => $difference]).'</small>',
			 "difference"=> $difference,
			 "my_address"=>$wallet->wallet_address,
			 "relativeTime" => Yii::$app->formatter->asDuration($difference),
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
