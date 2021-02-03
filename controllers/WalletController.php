<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

use app\models\BoltTokens;
use app\models\BoltTokensSearch;
use app\models\BoltWallets;


Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';

class WalletController extends Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
					'access' => [
							'class' => AccessControl::className(),
							'only' => ['saveSubscription', 'index'],
							'rules' => [
									[
											'allow' => true,
											'actions' => ['saveSubscription'],
											'roles' => ['?'],
									],
									[
											'allow' => true,
											'actions' => ['index'],
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
	 * Lists all models.
	 */
	public function actionIndex()
	{
		// carico il wallet selezionato nei settings
		$settings = \settings::loadUser(Yii::$app->user->id);
		if (empty($settings->id_wallet)){
			$fromAddress = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = BoltWallets::find()
	    		->andWhere(['id_wallet'=>$settings->id_wallet])
	    		->one();

			$fromAddress = $wallet->wallet_address;
		}

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
		]);


	}



	/**
	 * Funziona che salva esclusivamente l'indirizzo generato da eth-lightwallet
	 */
	public function actionSaveAddress()
	{
		$settings=Settings::load();

		if ($settings === null){
			echo CJSON::encode(array("error"=>'Errore: I parametri di configurazione per la connessione al nodo POA non sono stati trovati'));
			exit;
		}
		$block = 0;
		//if( webRequest::checkUrl( $settings->poa_url ) ) {
			// mi connetto al nodo poa
			// $web3 = new Web3($settings->poa_url);
			// $web3 = new Web3(WebApp::getPoaNode());
		$poaNode = WebApp::getPoaNode();
		if (!$poaNode){
			$save = new Save;
			$save->WriteLog('bolt','wallet','saveAddress',"All Nodes are down.");
		}else{
			$web3 = new Web3($poaNode);
			$eth = $web3->eth;
			//recupero l'ultimo block number

			$eth->getBlockByNumber('latest',false, function ($err, $response) use (&$block){
				if ($err !== null) {
					echo CJSON::encode(array("error"=>'Error: ' . $err->getMessage()));
					exit;
				}
				$block = $response->number;
			});
		}
		//}

		// se esiste aggiorno, altrimneti aggiungo
		// in questa ricerca devo trovare l'id_user e non il wallet address
		// se un user è già inserito aggiorno l0indirizzo e non il contrario
		$wallets=Wallets::model()->findByAttributes(array(
			//'wallet_address'=>$_POST['address'],
			'id_user' => Yii::app()->user->objUser['id_user']
		));
		if ($wallets === null){
			$wallets = new Wallets;
		}
		// salvo il nuovo indirizzo
		$wallets->id_user = Yii::app()->user->objUser['id_user'];
		$wallets->wallet_address = $_POST['address'];

		// metto il blocco a 0, in modo che se è un ripristino wallet, carica di nuovo tutte le transazioni
		// si potrebbe migliorare
		// TODO!!
		// cercare l'ultima transazione token in db con quel wallet e recuperare il numero blocco
		// in modo da non dover cercare nella blockchain dall'inizio
		$wallets->blocknumber = $block;

		if ($wallets->save()){
			//assegno il nuovo indirizzo all'utente
			Settings::saveUser($wallets->id_user,$wallets->attributes,array('id_wallet'));
			$result = array(
				'success'=>true,
				'wallet'=>$wallets->wallet_address
			);
		}else{
			$result = array(
				'success'=>false,
				'wallet'=>$_POST['address']
			);
		}

		echo CJSON::encode($result);
	}





	/**
	 * @param POST string address the Ethereum Address to be rescanned
	 */
	public function actionRescan(){
        //azzero il nuomero dei blocchi dell'indirizzo
		$model = Wallets::model()->findByAttributes(array('wallet_address'=>$_POST['wallet']));
		$model->blocknumber = '0x0';
		$model->update();

		echo CJSON::encode(array(
			'wallet' => $_POST['wallet'],
			"blocknumber"=>'0x0',
		));
	}

	/**
	 * @param POST string address the Ethereum Address to be paid
	 */
	public function actionCheckAddress(){
        // $settings=Settings::load();
		//
		// if( !webRequest::checkUrl( $settings->poa_url ) ) {
		// 	echo CJSON::encode(array(
		// 		'id'=>time(),
		// 		'response'=>false,
		// 	));
		// 	return;
		// }
        // mi connetto al nodo poa
		// $web3 = new Web3($settings->poa_url);
		// $web3 = new Web3(WebApp::getPoaNode());
		$poaNode = WebApp::getPoaNode();
		if (!$poaNode){
			$save = new Save;
			$save->WriteLog('bolt','wallet','checkAddress',"All Nodes are down.");
				echo CJSON::encode(array(
					'id'=>time(),
					'response'=>false,
				));
				return;
		}
		$web3 = new Web3($poaNode);
		$utils = $web3->utils;
		$response = $utils->isAddress($_POST['to']);


		echo CJSON::encode(array(
			'id' => $_POST['to'],
			"response"=>$response,
		));
	}



	public function actionEstimateGas(){
		$fromAccount = $_POST['from'];
		$toAccount = $_POST['to'];
		$amount = $_POST['amount'];

		// $settings=Settings::load();

		// if( !webRequest::checkUrl( $settings->poa_url ) ) {
		// 	echo CJSON::encode(array(
		// 		'id'=>time(),
		// 		'success'=>false,
		// 	));
		// 	return;
		// }
        // mi connetto al nodo poa
		// $web3 = new Web3($settings->poa_url);
		// $web3 = new Web3(WebApp::getPoaNode());
		$poaNode = WebApp::getPoaNode();
		if (!$poaNode){
			$save = new Save;
			$save->WriteLog('bolt','wallet','estimateGas',"All Nodes are down.");
				echo CJSON::encode(array(
			 		'id'=>time(),
					'error'=>"All Nodes are down.",
			 		'success'=>false,
			 	));
			 	return;
		}
		$web3 = new Web3($poaNode);

		$eth = $web3->eth;
		$personal = $web3->personal;
		$utils = $web3->utils;
		$hex = $utils->toWei($amount, 'ether')->toHex();

		$gasPrice = null;
		// estimateGas
	    $eth->estimateGas([
	        	'from' => $fromAccount,
	        	'to' => $toAccount,
	        	'value' => '0x'.$hex
	    	], function ($err, $gas) use ($utils, $eth, $fromAccount, $toAccount, &$gasPrice) {
	        	if ($err !== null) {
	            	echo CJSON::encode(array("error"=>$err->getMessage()));
	            	exit;
	        	}
				$value = (string) $gas * 1;
				$gasPrice = $value / pow(10,8);
	    });
		//echo '<pre>'.print_r($gasPrice,true).'</pre>';
		//exit;
		$send_json = array(
			'gasPrice' => $gasPrice,
			'id' => time(), // id ci deve essere per il s.w.
		);
    	echo CJSON::encode($send_json);
	}


	/**
	 * Saves the Subscription for push messages.
	 * @param POST VAPID KEYS
	 * this function NOT REQUIRE user to login
	 */
	public function actionSaveSubscription()
	{
		ini_set("allow_url_fopen", true);
		//
 		$raw_post_data = file_get_contents('php://input');
 		if (false === $raw_post_data) {
 			throw new \Exception('Could not read from the php://input stream or invalid Subscription object received.');
 		}
 		$raw = json_decode($raw_post_data);
		$browser = $_SERVER['HTTP_USER_AGENT'];

		$Criteria=new CDbCriteria();
		$Criteria->compare('id_user',Yii::app()->user->objUser['id_user'], false);
		$Criteria->compare('browser',$browser, false);

		$vapidProvider=new CActiveDataProvider('PushSubscriptions', array(
			'criteria'=>$Criteria,
		));

		if ($vapidProvider->totalItemCount == 0 && $raw != null ){
			//save
			$vapid = new PushSubscriptions;
			$vapid->id_user = Yii::app()->user->objUser['id_user'];
			$vapid->browser = $browser;
			$vapid->endpoint = $raw->endpoint;
			$vapid->auth = $raw->keys->auth;
			$vapid->p256dh = $raw->keys->p256dh;
			$vapid->type = 'wallet';

			if (!$vapid->save()){
				echo '[WalletController] SaveSubscription: Cannot save subscription on server!';
				exit;//
			}
			echo '[WalletController] SaveSubscription: Subscription saved on server!';
		}else{
			//delete
			$iterator = new CDataProviderIterator($vapidProvider);
			foreach($iterator as $data) {
				echo print_r($data->id_subscription,true).',';
				#exit;
				$vapid=PushSubscriptions::model()->findByPk($data->id_subscription)->delete();

				// if($vapid!==null)
				// 	$vapid->delete();
			}
			echo '[WalletController] SaveSubscription: Subscriptions deleted on server!';
		}
	}
}
