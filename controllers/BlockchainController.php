<?php namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

use app\models\MPWallets;
use app\models\Transactions;
use app\models\SmartContracts;

use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use app\components\Settings;
use app\components\WebApp;
use app\components\Messages;
use app\components\Erc20;

class BlockchainController extends Controller
{
	public $transactionsFound = [];
	public $logFileName;

	private function setTransactionsFound($transaction){
        $this->transactionsFound[] = $transaction;
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
	public function actionCheckLatest()
	{
		// return true;
		// echo '<pre>'.print_r($_POST,true).'</pre>';
		// exit;
		set_time_limit(30); //imposto il time limit unlimited

		// $this->logFileName = Yii::$app->basePath."/logs/blockchain-latest.log";

		//carico info del wallet
		$wallets = MPWallets::find()
		   ->andWhere(['id_user'=>Yii::$app->user->id])
		   ->one();

		if (null === $wallets){
			return Json::encode(['success'=>false]);
		}

		$SEARCH_ADDRESS = strtoupper($wallets->wallet_address);

		//Carico i parametri della webapp
		// $settings = Settings::poa();

		$ERC20 = new Yii::$app->Erc20();
		$blockLatest = $ERC20->getBlockInfo();

		if (!is_object($blockLatest))
			return Json::encode(['success'=>false]);


		$chainBlock = $blockLatest->number;
		$savedBlock = '0x'. dechex (hexdec($blockLatest->number) -14 );

		// carico tutti gli smartcontract dell'utente
		$smartContracts = SmartContracts::find()->where(['id_user'=>Yii::$app->user->id])->all();
		$smartContractsArray = ArrayHelper::map($smartContracts, 'id',
				function($data) {
					return strtoupper($data->smart_contract_address);
				}
		);
		foreach ($smartContracts as $key => $value) {
			// code...
			$smartContractsById[$value->id] = $value;
		}

		// echo '<pre>'.print_r($smartContractsById,true).'</pre>';;exit;


		//
		// $test = '0X82D50AD3C1091866E258FD0F1A7CC9674609D254';
		// $result = array_search($test,$smartContractsArray,true);
		// if (in_array($test,$smartContractsArray,true)){
		// 	echo $test.' is in array and id is: '.array_search($test,$smartContractsArray);
		// } else {
		// 	echo $test." is not in array";
		// }
		// echo '<pre>'.print_r($smartContractsArray,true).'</pre>';;
		// echo '<pre>'.print_r($result,true).'</pre>';;;
		// echo '<pre>'.print_r($smartContractsById[$result],true).'</pre>';exit;

		// Inizio il ciclo sui blocchi
		for ($x=0; $x <= 15; $x++)
		{
			if ((hexdec($savedBlock)+$x) <= hexdec($chainBlock)){
				$transactions = [];
				//somma del valore del blocco in decimali
				$searchBlock = '0x'. dechex (hexdec($savedBlock) + $x );
			   	// ricerco le informazioni del blocco tramite il suo numero
				$block = $ERC20->getBlockInfo($searchBlock,true);

				if (is_object($block))
					$transactions = $block->transactions;

				// echo '<pre>'.print_r($block,true).'</pre>';exit;
				// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transactions on block n. $searchBlock: \n".'<pre>'.print_r($transactions,true).'</pre>');
				// exit;

				if (!empty($transactions))
				{
					// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : La transazione non è vuota\n");
					foreach ($transactions as $transaction)
					{
						// if (in_array($transaction->to,$smartContracts)){
						// 	echo $transaction->to.' is in array and id is: '.array_search($transaction->to,$smartContracts);
						// } else {
						// 	echo $transaction->to."is not in array";
						// }
						// echo '<pre>'.print_r($smartContracts,true).'</pre>';exit;

						//controlla transazioni ethereum
						//if (strtoupper($transaction->to) <> strtoupper($settings->smartContract->smart_contract_address) ){
						if (!in_array(strtoupper($transaction->to), $smartContractsArray)){
							// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione ether...\n");
							$ReceivingType = 'ether';
					    }else{
							$smartContractId = array_search(strtoupper($transaction->to),$smartContractsArray,true);

						   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione token...\n");
						   //smart contract
						   $ReceivingType = 'token';
						   // $transactionId = $transaction->hash;
						   // recupero la ricevuta della transazione tramite hash
						   $transactionContract = $ERC20->getReceipt($transaction->hash);

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
								   $tokens = Transactions::find()->findByHash($transactionContract->transactionHash);

								   // SE da DB è null, è NECESSARIA LA NOTIFICA per chi invia e riceve
								   if (null===$tokens){
									   //$save->WriteLog('bolt','blockchain','SyncBlockchain',"Transaction found but it isn\'t in DB. I\'ll save it in DB.");
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction on blockchain found but it isn\'t in DB. I\'ll save it in DB....\n");

									   //salva la transazione
									   $timestamp = 0;
									   $transactionValue = $ERC20->wei2eth(
										   $transactionContract->logs[0]->data,
										   $smartContractsById[$smartContractId]->decimals
									   ); // decimali del token
									   //$settings->smartContract->decimals
									   // $rate = 1; //eth::getFiatRate('token');

									   // con questa funzione recupero il timestamp in cui è stata minata
									   // la  transazione
									   // NB: il timestamp è quello sul server POA.

									   $blockByHash = $ERC20->getBlockByHash($transaction->blockHash);

									   // salvo la transazione NULL in db. Restituisce object
									   $tokens = new Transactions;
									   $tokens->id_user = $wallets->id_user;
							           $tokens->status	= 'complete';
							           $tokens->type	= 'token';
									   $tokens->id_smart_contract = $smartContractId; //$settings->smartContract->id;
							           $tokens->token_price	= $transactionValue;
							           $tokens->token_received	= $transactionValue;
							           $tokens->invoice_timestamp = hexdec($blockByHash->timestamp);
							           $tokens->expiration_timestamp = hexdec($blockByHash->timestamp) + 60*15;
							           $tokens->from_address = $transaction->from;
							           $tokens->to_address = $receivingAccount;
							           $tokens->blocknumber = $transactionContract->blockNumber;
							           $tokens->txhash = $transactionContract->transactionHash;
									   $tokens->message = '';
									   $tokens->save();

									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Saving transaction: <pre>".print_r($tokens->attributes,true)."</pre>\n");
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction errors: <pre>".print_r($tokens->errors,true)."</pre>\n");

									   $dateLN = date("d M `y",$tokens->invoice_timestamp);
							           $timeLN = date("H:i:s",$tokens->invoice_timestamp);

									   $id_user_from = MPWallets::find()->userIdFromAddress($tokens->from_address);
									   if ($id_user_from !== null){
										   // notifica per chi ha inviato (from_address)
										   $notification = [
											   'type' => 'token',
											   'id_user' => $id_user_from,
											   'status' => 'complete',
											   'description' => Yii::t('app','A transaction you sent of {amount} {symbol} has been completed.',[
												   'amount' => $tokens->token_price,
												   'symbol' => $smartContractsById[$smartContractId]->symbol, //$settings->smartContract->symbol,
											   ]),
	                                           'url' => Url::to(['/transactions/view','id'=>WebApp::encrypt($tokens->id)],true),
											   'timestamp' => $tokens->invoice_timestamp,
											   'price' => $tokens->token_price,
										   ];

	                                       // $this->log("quindi salvo il primo messaggio\n: <pre>".print_r($notification,true)."</pre>\n");
	                                       $messages= Messages::push($notification);
									   }
                                       // $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");

									   // $save->Notification($notification);

									   // notifica per chi riceve (to_address)
									   $id_user_to = MPWallets::find()->userIdFromAddress($tokens->to_address);

                                       // perchè id_user_to === null  ???
                                       // potrebbe accadere che la trtansazione viene inviata da
                                       // METAMASK o da altra applicazione quindi non trovo
                                       // l'indirizzo di quel particolare user nella tabella
                                       // quindi NON INVIO il messaggio
                                       if ($id_user_to !== null){
                                           $notification['id_user'] = $id_user_to;
    									   $notification['description'] = Yii::t('app','You received a new transaction of {amount} {symbol}.',[
											   'amount' => $tokens->token_price,
											   'symbol' => $smartContractsById[$smartContractId]->symbol, //$settings->smartContract->symbol,
										   ]);

                                            // $this->log("quindi salvo il secondo messaggio\n: <pre>".print_r($notification,true)."</pre>\n");
                                           $messages= Messages::push($notification);
                                            // $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");
                                       } else {
                                            // $this->log("quindi NON invio il messaggio al DESTINATARIO, in quanto non trovato in tabella wallet");
                                       }



                                       // imposto l'array contenente le transazioni e che sarà restituito alla funzione chiamante
                                      $this->setTransactionsFound([
                                           'id_token' => $tokens->id,
                                           'pushoptions' => $messages,
										   'balance' => WebApp::number_shorten($ERC20->tokenBalance($wallets->wallet_address)),
                                           'row' => WebApp::showTransactionRow($tokens,$wallets->wallet_address,true),
                                      ]);

									  // $this->log("l'array settransactionFound è: <pre>".print_r($this->getTransactionsFound(),true)."</pre>\n");




								   }else{
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction on blockchain found and it is found also on DB....\n");
									   // se NON è NULL notifico solo il ricevente
									   if (strtoupper($tokens->to_address) == $SEARCH_ADDRESS){
										   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : The search address is to_address....\n");

										   if ($tokens->token_received == 0 ){ //&& $tokens->status <> 'complete'){
											   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Ricevuto è 0....\n");

											   $dateLN = date("d M `y",$tokens->invoice_timestamp);
											   $timeLN = date("H:i:s",$tokens->invoice_timestamp);
                                               $tokens->status = 'complete';
                                               $tokens->token_received = $tokens->token_price;

											   $tokens->update();
											    // $this->log("allora ho aggiornato tabella tokens....\n");

                                               //salva la notifica
											   $notification = [
												  'type' => 'token',
												  'id_user' => Yii::$app->user->id,
												  'status' => 'complete',
												  'description' => Yii::t('app','You received a new transaction.'),
                                                  'url' => 'index.php?r=transactions/view&id='.WebApp::encrypt($tokens->id),
												  'timestamp' => $tokens->invoice_timestamp,
												  'price' => $tokens->token_price,
											  ];

                                               // $this->log("quindi salvo messaggio 3\n: <pre>".print_r($notification,true)."</pre>\n");

                                              $messages= Messages::push($notification);
                                               // $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");

                                              $this->setTransactionsFound([
                                                  'id_token' => $tokens->id,
                                                 'pushoptions' => $messages,
												 'balance' => WebApp::number_shorten($ERC20->tokenBalance($wallets->wallet_address)),
                                                 'row' => WebApp::showTransactionRow($tokens,$wallets->wallet_address,true),
                                              ]);
											  // $this->log("l'array settransactionFound è: <pre>".print_r($this->getTransactionsFound(),true)."</pre>\n");
										   }
									   }else{
										   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : The search address non è to_address e quindi non faccio nulla....\n");
									   } // notifica al ricevente
								   } // tabella tokens found

							   } // if ricevi o hai inviato
						   }// se transaction contract != ''
					   } //endif 'ethereum or token'
				   } // per ogni transazione trovata
			   }// if not empty transaction

			   //aggiorno il numero dei blocchi sul wallet
			   // print_r($searchBlock);
			   // $wallets->blocknumber = $searchBlock;
			   // $wallets->update();
		   }else{
			   break;
		   }
	   } // ciclo for
	   // // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Searching for transactions. Latest block #: $searchBlock: \n");

	   $difference = hexdec($chainBlock) - hexdec($searchBlock);
	   // echo "\r\n<pre>differenza è: $difference</pre>";
	   // echo "\r\n<pre>txfound è : ".$this->getTransactionsFound() ."</pre>";
	   // echo '\r\n<pre>il transactiopn found finale è'.print_r($this->getTransactionsFound(),true).'</pre>';

	    if (is_object($block))
	   		$timeToComplete = time() - hexdec($block->timestamp);
		else
			$timeToComplete = time();

		$txFound = $this->getTransactionsFound();
	    // if (!(empty($txFound)))
	    //     $this->log("getTransaction: <pre>".print_r($txFound,true)."</pre>\n");


	   $return = [
	   	// 'id'=>time(),
	   	'success'=>true,
	   	'message'=>'response from check-transactions',

	   	'searchFromBlockNumber' => $savedBlock,
	   	// "headerMessage"=> Yii::t('app', "{n} blocks left.", ['n' => $difference]),
	   	"transactions"=>$txFound,
	   	 "walletBlocknumber"=>$wallets->blocknumber,
	   	 "chainBlocknumber"=>$chainBlock,
	   	 "headerMessage"=> Yii::t('app', "{n} blocks left.", ['n' => $difference]),
	   	 "difference"=> $difference,
	   	 "user_address"=>$wallets->wallet_address,
	   	 "relativeTime" => Yii::$app->formatter->asDuration($timeToComplete),
	   ];

	   // $this->log("<pre>il test found finale è: ".print_r($return,true)."</pre>");

		return $this->json($return);

	}


	// recupera il blocknumber attuale della blockchain
	// recupera il blocknumber del wallet utente
	// mostra la differenza dei blocchi
	public function actionGetBlocknumber(){
		$wallet = MPWallets::find()
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

		$ERC20 = new Yii::$app->Erc20(1);

		$blockLatest = $ERC20->getBlockInfo();
		// $blockWallet = Yii::$app->Erc20->getBlockInfo($wallet->blocknumber);
		// print_r($block);
		// exit;

		//calcolo la differenza tra i blocchi
		$difference = hexdec($blockLatest->number) - hexdec($wallet->blocknumber);

		$return = [
			 'id'=>time(),
			 "walletBlocknumber"=>$wallet->blocknumber,
			 "chainBlocknumber"=>$blockLatest->number,
			 "headerMessage"=> Yii::t('app', "{n} blocks left.", ['n' => $difference]),
			 "difference"=> $difference,
			 "my_address"=>$wallet->wallet_address,
			 "relativeTime" => Yii::$app->formatter->asDuration($difference),
			 "success"=>true,
		];

		return $this->json($return);

	}

	//scrive nel file log le informazioni richieste
    private function log($text){
        //$filename = Yii::$app->basePath."/logs/blockchain-latest.log";
        $handlefile = fopen($this->logFileName, "a");

		$time = "\r\n" .date('Y/m/d h:i:s a - ', time());
		fwrite($handlefile, $time.$text);
    }






	private static function json ($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $data;
	}







}
