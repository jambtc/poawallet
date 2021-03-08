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
use app\models\MPWallets;
use app\models\Users;
use app\models\SendTokenForm;
use app\models\WizardWalletForm;
use app\models\PushSubscriptions;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Url;

use app\components\WebApp;
use Nullix\CryptoJsAes\CryptoJsAes;

use app\components\Settings;
use app\components\Messages;


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
		$user = Users::find()
 	     		->andWhere(['id'=>Yii::$app->user->id])
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
 		$fromAddress = MPWallets::find()->userAddress(Yii::$app->user->id);
		if (null === $fromAddress){
			$session = Yii::$app->session;
			$string = Yii::$app->security->generateRandomString(32);
			$session->set('token-wizard', $string );
			return $this->redirect(['wizard/index','token' => $string]);
		}

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

		// imposto il valore del nonce attuale
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
				'chainId' => $settings->poa_chainId,
				'decryptedSign' => $decrypted,
			]);

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

		//salva la transazione ERC20 in archivio
		$timestamp = time();
		$invoice_timestamp = $timestamp;

		//calcolo expiration time
		$totalseconds = $settings->poa_expiration * 60; //poa_expiration è in minuti, * 60 lo trasforma in secondi
		$expiration_timestamp = $timestamp + $totalseconds; //DEFAULT = 15 MINUTES

		//$rate = $this->getFiatRate(); // al momento il token è peggato 1/1 sull'euro
		$rate = 1; //eth::getFiatRate('token'); //

		$tokens = new BoltTokens;
		$tokens->id_user = Yii::$app->user->id;
		$tokens->status = 'new';
		$tokens->type = 'token';
		$tokens->token_price = $amount;
		$tokens->token_ricevuti = 0;
		$tokens->fiat_price = abs($rate * $amount);
		$tokens->currency = 'EUR';
		$tokens->item_desc = 'wallet';
		$tokens->item_code = '0';
		$tokens->invoice_timestamp = $invoice_timestamp;
		$tokens->expiration_timestamp = $expiration_timestamp;
		$tokens->rate = $rate;
		$tokens->from_address = $fromAccount;
		$tokens->to_address = $toAccount;
		$tokens->blocknumber = hexdec($block->number);
		$tokens->txhash = $tx;
		$tokens->memo = $memo;

		if (!($tokens->save())){
			throw new HttpException(404,print_r($tokens->errors));
		}


		// notifica per chi ha inviato (from_address)
		$notification = [
			'type_notification' => 'token',
			'id_user' => Yii::$app->user->id,
			'id_tocheck' => $tokens->id_token,
			'status' => 'new',
			'description' => Yii::t('app','You sent a new transaction.'),
			'url' => Url::to(["/tokens/view",'id'=>WebApp::encrypt($tokens->id_token)]),
			'timestamp' => time(),
			'price' => $tokens->token_price,
			'deleted' => 0,
		];
		Messages::push($notification);

		//adesso posso uscire CON IL JSON DA REGISTRARE NEL SW.
		$data = [
			'id' => $WebApp->encrypt($tokens->id_token), //NECESSARIO PER IL SALVATAGGIO IN  indexedDB quando ritorna al Service Worker
			'status' => $tokens->status,
			'url' => Url::to(['/send/validate-transaction']),
			'row' => $WebApp->showTransactionRow($tokens,$fromAccount),
		];

		return $this->json($data);
 	}

	// cerca la ricevuta dal transaction hash
	// funzione invocata dal sw

	// testing::
	// curl -X POST -d 'id=S2hNeTVGQTkzWis0ekN3RDV3RVRmdz09' http://localhost/megapay/web/index.php?r=wallet%2Fvalidate-transaction
	public function actionValidateTransaction()
	{
		set_time_limit(0); //imposto il time limit unlimited
		$maxrequests = 30;
		$requests = 1;

		$WebApp = new WebApp;
		$settings = Settings::load();

		$tokens = BoltTokens::find()
 	     		->andWhere(['id_token'=>$WebApp->decrypt($_POST['id'])])
 	    		->one();

		while ($requests < $maxrequests)
		{
			$receipt = Yii::$app->Erc20->getReceipt($tokens->txhash);

			if ($receipt !== null){
				// ok, the transaction has mind in a new block
				break;
			}
			$requests ++;
			sleep(1);
		}
		if ($receipt === null){
			$data = [
				'id' => $_POST['id'], //NECESSARIO PER IL SALVATAGGIO IN  indexedDB quando ritorna al Service Worker
				'status' => $tokens->status,
				'success' => false,
			];
		} else {
			$tokens->status = 'complete';
			$tokens->token_ricevuti = $tokens->token_price;
			$tokens->blocknumber = hexdec($receipt->blockNumber);

			if (!($tokens->save())){
				throw new HttpException(404,$tokens->errors);
			}

			// notifica per chi ha inviato (from_address)
			$id_user_from = MPWallets::find()->userIdFromAddress($tokens->from_address);
			$notification = [
				'type_notification' => 'token',
				'id_user' => $id_user_from,
				'id_tocheck' => $tokens->id_token,
				'status' => 'complete',
				'description' => Yii::t('app','A transaction you sent has been completed.'),
				'url' => Url::to(["/tokens/view",'id'=>WebApp::encrypt($tokens->id_token)]),
				'timestamp' => time(),
				'price' => $tokens->token_price,
				'deleted' => 0,
			];
			$pushOptions = Messages::push($notification);

			// notifica per chi riceve (to_address)
			$id_user_to = MPWallets::find()->userIdFromAddress($tokens->to_address);
			$notification['id_user'] = $id_user_to === null ? 1 : $id_user_to;
			$notification['description'] = Yii::t('app','A transaction you received has been completed.');
			Messages::push($notification);

			//adesso posso uscire CON IL JSON DA REGISTRARE NEL SW.
			$data = [
				'id' => $_POST['id'], //NECESSARIO PER IL SALVATAGGIO IN  indexedDB quando ritorna al Service Worker
				'status' => $tokens->status,
				'success' => true,
				'row' => $WebApp->showTransactionRow($tokens,$tokens->from_address),
				'balance' => Yii::$app->Erc20->Balance($tokens->from_address),
				'pushoptions' => $pushOptions,
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
