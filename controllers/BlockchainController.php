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
use app\models\Ethtxs;
use app\models\EthtxsStatus;
use app\models\Nodes;

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

	public function actionEthtx()
	{
		$raw_post_data = file_get_contents('php://input');
        $_POST = Json::decode($raw_post_data);
		$user_id = WebApp::decrypt($_POST['user_id']);
		// echo '<pre>'.print_r($_POST,true).'</pre>';

		$wallets = MPWallets::find()->where(['id_user'=>$user_id])->one();
		if (null === $wallets){
			return Json::encode(['success'=>false]);
		}
		$node = Nodes::find()->where(['id_user'=>$user_id])->one();

		if (null === $node){
			return Json::encode(['success'=>false]);
		}

		$fromAddress = $wallets->wallet_address;
		$timeToComplete = 0;

		$ethTxsStatus = EthtxsStatus::find()->where(['id_blockchain'=>$node->id_blockchain])->one();
		$searchBlock = $ethTxsStatus->blocknumber;

		$ethtxs = Ethtxs::find()
            ->orwhere(['=','txfrom', $fromAddress])
            ->orwhere(['=','txto', $fromAddress])
            ->all();


		foreach ($ethtxs as $data){
			// echo '<pre>'.print_r($data,true).'</pre>';
			// opero solo se è una transazione token
			if ($data->contract_to != ''){
				$smartContracts = SmartContracts::find()->where(['smart_contract_address'=>$data->contract_to])->one();

				if (null === $smartContracts)
					continue;

				// cerco la transazione nella tabella transactions tramite txhash
				$tokens = Transactions::find()->findByHash($data->txhash);

				if (null === $tokens){
					// ho trovato una transazinoe che non è nel DB.
					$this->saveAndSendPushMessages([
						'userid' => $user_id,
						'smartContracts' => $smartContracts,
						'data' => $data,
						'fromAddress' => $fromAddress
					]);
				}
			}
		}
		$wallets->blocknumber = $searchBlock;
		$wallets->save();

		$txFound = $this->getTransactionsFound();
		$chainBlock = $this->getChainBlock($user_id);
		$walletBlock = $this->getChainBlock($user_id, $searchBlock);
		$timeToComplete = hexdec($chainBlock->timestamp) - hexdec($walletBlock->timestamp);

		$difference = hexdec($chainBlock->number) - hexdec($searchBlock);
        $percentageCompletion = round(hexdec($searchBlock) / hexdec($chainBlock->number) * 100, 4);

	   	$return = [
			'success'=>true,
			"transactions"=>$txFound,
			'searchFromBlockNumber' => $searchBlock,
            "walletBlocknumber"=> $wallets->blocknumber,
            "chainBlocknumber"=> $chainBlock->number,
            "headerMessage"=> Yii::t('app', "{n} blocks left.", ['n' => $difference]),
            "difference"=> $difference,
            "user_address"=> $fromAddress,
            "relativeTime" => Yii::$app->formatter->asDuration($timeToComplete),
            "percentageCompletion" => $percentageCompletion."%",
            "latestBlockHash"=>Html::a($chainBlock->hash,$node->blockchain->url_block_explorer.'/block/'.hexdec($chainBlock->number),['target'=>'_blank']),
            "walletBlockHash"=>Html::a($walletBlock->hash,$node->blockchain->url_block_explorer.'/block/'.hexdec($searchBlock),['target'=>'_blank']),
	   	];
	   	return Json::encode($return);
	}

	private function getChainBlock($userid,$number='latest'){
		$ERC20 = new Yii::$app->Erc20($userid);
		while (true){
			$blockInfo = $ERC20->getBlockInfo($number);
			if (null !== $blockInfo){
				return $blockInfo;
				break;
			}
		}
	}


	private function saveAndSendPushMessages($array){
		$tokens = new Transactions;
		$tokens->id_user = $array['userid'];
		$tokens->status	= 'complete';
		$tokens->type	= 'token';
		$tokens->id_smart_contract = $array['smartContracts']->id; //$settings->smartContract->id;
		$tokens->token_price	= $array['data']->contract_value;
		$tokens->token_received	= $array['data']->contract_value;
		$tokens->invoice_timestamp = $array['data']->timestamp;
		$tokens->expiration_timestamp = $array['data']->timestamp + 60*15;
		$tokens->from_address = $array['data']->txfrom;
		$tokens->to_address = $array['data']->txto;
		$tokens->blocknumber = $array['data']->blocknumber;
		$tokens->txhash = $array['data']->txhash;
		$tokens->message = '';
		$tokens->save();

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
					'symbol' => $array['smartContracts']->symbol, //$settings->smartContract->symbol,
				]),
				'url' => Url::to(['/transactions/view','id'=>WebApp::encrypt($tokens->id)],true),
				'timestamp' => $tokens->invoice_timestamp,
				'price' => $tokens->token_price,
			];

			$messages= Messages::push($notification);
		}
		// notifica per chi riceve (to_address)
		$id_user_to = MPWallets::find()->userIdFromAddress($tokens->to_address);
		if ($id_user_to !== null){
			$notification['id_user'] = $id_user_to;
			$notification['description'] = Yii::t('app','You received a new transaction of {amount} {symbol}.',[
				'amount' => $tokens->token_price,
				'symbol' => $array['smartContracts']->symbol, //$settings->smartContract->symbol,
			]);
			$messages= Messages::push($notification);
		}

		$ERC20 = new Yii::$app->Erc20($array['userid']);

		// imposto l'array contenente le transazioni e che sarà restituito alla funzione chiamante
		$this->setTransactionsFound([
			'id_token' => $tokens->id,
			'pushoptions' => $messages,
			'balance' => WebApp::number_shorten($ERC20->tokenBalance($array['fromAddress'])),
			'row' => WebApp::showTransactionRow($tokens,$array['fromAddress'],true),
		]);
	}

	// verifica eventuali transazioni dell'utente a partire dal getBlockNumber
	// dell'utente fino ad arrivare all'ultimo blocco della blockchain
	public function actionCheckLatest()
	{
		set_time_limit(30); //imposto il time limit unlimited

		$user_id = Yii::$app->user->id;

		//carico info del wallet
		$wallets = MPWallets::find()->where(['id_user'=>$user_id])->one();

		if (null === $wallets){
			return Json::encode(['success'=>false]);
		}

		$fromAddress = $wallets->wallet_address;

		//$SEARCH_ADDRESS = strtoupper($wallets->wallet_address);

		//Carico i parametri della webapp
		// $settings = Settings::poa();
		// $ERC20 = new Yii::$app->Erc20($user_id);

		// $blockLatest = $ERC20->getBlockInfo();
		$blockLatest = $this->getChainBlock($user_id);

		// if (!is_object($blockLatest))
		// 	return Json::encode(['success'=>false]);

		$chainBlock = $blockLatest->number;
		$savedBlock = '0x'. dechex (hexdec($blockLatest->number) -14 );

		// // carico tutti gli smartcontract dell'utente
		// $smartContracts = SmartContracts::find()->where(['id_user'=>Yii::$app->user->id])->all();
		// $smartContractsArray = ArrayHelper::map($smartContracts, 'id',
		// 		function($data) {
		// 			return strtoupper($data->smart_contract_address);
		// 		}
		// );
		// foreach ($smartContracts as $key => $value) {
		// 	// code...
		// 	$smartContractsById[$value->id] = $value;
		// }

		// Inizio il ciclo sui blocchi
		for ($x=0; $x <= 15; $x++)
		{
			if ((hexdec($savedBlock)+$x) <= hexdec($chainBlock)){
				//$transactions = [];
				//somma del valore del blocco in decimali
				$searchBlock = '0x'. dechex (hexdec($savedBlock) + $x );
			   	// ricerco le informazioni del blocco tramite il suo numero
				// $block = $ERC20->getBlockInfo($searchBlock,true);
				$block = $this->getChainBlock($user_id, $searchBlock);

				// if (is_object($block))
				// 	$transactions = $block->transactions;


				if (!empty($transactions))
				{
					// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : La transazione non è vuota\n");
					foreach ($transactions as $idx => $trans)
					{
						$inputinfo = $trans->input;
						$inputinit = substr($inputinfo,0,10);

						# Check if transaction is a contract transfer
						if ($trans->value == '0x0' && $inputinit != '0xa9059cbb') {
							continue;
						}

						# Check if transaction is a contract transfer
						if ($inputinit == '0xa9059cbb') {
							$smartContracts = SmartContracts::find()->where(['smart_contract_address'=>$trans->to])->one();
							if (null === $smartContracts) {
								continue;
							}


							$data = [
								'contract_value' => hexdec(substr($inputinfo,-64)),
								'timestamp' => hexdec($block->timestamp),
								'txfrom' => $trans->from,
								'txto' => '0x'.substr($inputinfo,34,40),
								'blocknumber' => $block->number,
								'txhash' => $trans->hash,
							];
							// cerco la transazione nella tabella transactions tramite txhash
							$tokens = Transactions::find()->findByHash($txhash);

							if (null === $tokens){
								// ho trovato una transazinoe che non è nel DB.
								$this->saveAndSendPushMessages([
									'userid' => $user_id,
									'smartContracts' => $smartContracts,
									'data' => $data,
									'fromAddress' => $fromAddress
								]);
							}

						}

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


	   	$timeToComplete = time() - hexdec($block->timestamp);

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
