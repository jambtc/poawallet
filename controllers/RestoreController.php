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

use app\models\WizardWalletForm;
use app\models\MPWallets;
use app\models\Nodes;
use app\models\Users;
use app\models\StandardBlockchainValues;
use app\models\StandardSmartContractValues;
use app\models\Blockchains;
use app\models\SmartContracts;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Url;

class RestoreController extends Controller
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


	/**
	 * Show Restore old wallet page
	 */
	public function actionIndex()
 	{
		$this->layout = 'wizard';

		$defaultNetworkExist = false;

		$formModel = new WizardWalletForm; //form di input dei dati

		if (Yii::$app->request->isAjax && $formModel->load(Yii::$app->request->post())) {
		    Yii::$app->response->format = Response::FORMAT_JSON;
			// echo '<pre>'.print_r(ActiveForm::validate($sendTokenForm),true).'</pre>';
		    return ActiveForm::validate($formModel);
		}

		if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
			// se sono giunto qui, l'indirizzo dell'utente non doveva essere in tabella
			// oppure non corrisponde a quello salvato in indexedDB
			$boltWallet = MPWallets::find()->where( [ 'id_user' => Yii::$app->user->id ] )->one();
			$node = Nodes::find()->where(['id_user'=>Yii::$app->user->id])->one();

			// echo '<pre> '.print_r($boltWallet,true);;
			// echo '<pre> '.print_r($node,true);;exit;
			// echo '<pre> '.print_r($user->getWallets(),true);exit;

			if(null === $boltWallet) {
				$boltWallet = new MPWallets;
				$boltWallet->id_user = Yii::$app->user->id;
				//$boltWallet->wallet_address = Yii::$app->request->post('WizardWalletForm')['address'];

				if (null === $node){
					$defaultNetworkExist = true;
					$node = $this->createDefaultNetworks();

					//$boltWallet->blocknumber = $this->getLatestBlockNumber();
					//$boltWallet->save();
					// entro nella richiesta di selezione del nodo
					// 2 sono stati già preinseriti
					 //return $this->redirect(['/settings/nodes/create']);
				}
				//  else {
				//
				//
				// }
				$boltWallet->blocknumber = $this->getLatestBlockNumber();
				// echo '<pre> [nodes]'.print_r($nodes,true);exit;
			}
			$boltWallet->wallet_address = Yii::$app->request->post('WizardWalletForm')['address'];

			if ($boltWallet->save()){
				if (null === $node && $defaultNetworkExist === false){
					$this->createDefaultNetworks();
				}
				// 	return $this->redirect(['/settings/nodes/create']);
				// } else {

				return $this->redirect(['/wallet/index']);
				// }
			}
			else
				var_dump( $boltWallet->getErrors());

			exit;
    	}

 		return $this->render('index', [
			'formModel' => $formModel,
		]);
 	}

	private function getLatestBlockNumber(){
		$ERC20 = new Yii::$app->Erc20(Yii::$app->user->id);
		$block = $ERC20->getBlockInfo();

		// echo '<pre>'.print_r($block,true);exit;
		$json = json_decode($block);

		if (!isset($json->error)){
			return ($block === null) ? '0x0' : $block->number;
		} else {
			return '0x0';
		}
	}

	private function createDefaultNetworks()
	{
		$default_blockchains = StandardBlockchainValues::find()->all();
		$default_smartcontracts = StandardSmartContractValues::find()->all();

		$blockchain = Blockchains::find()->where(['id_user'=>Yii::$app->user->id])->all();
		$smartcontract = SmartContracts::find()->where(['id_user'=>Yii::$app->user->id])->all();
		$nodes = Nodes::find()->where(['id_user'=>Yii::$app->user->id])->one();

		// echo '<pre>'.print_r($default_blockchains,true);;
		// echo '<pre>'.print_r($default_smartcontracts,true);;
		// echo '<pre>blockchain'.print_r($blockchain,true);;
		// echo '<pre>smart'.print_r($smartcontract,true);;
		// echo '<pre>nodes'.print_r($nodes,true);;
		if (empty($blockchain)) {
			foreach ($default_blockchains as $default_blockchain){
				$blockchain = new Blockchains;
				$blockchain->id_user = Yii::$app->user->id;
				$blockchain->denomination = $default_blockchain->denomination;
				$blockchain->chain_id = $default_blockchain->chain_id;
				$blockchain->url = $default_blockchain->url;
				$blockchain->symbol = $default_blockchain->symbol;
				$blockchain->url_block_explorer = $default_blockchain->url_block_explorer;
				$blockchain->zerogas = $default_blockchain->zerogas;
				if (!$blockchain->save()){
					var_dump( $blockchain->getErrors());
					die();
				}
			}
		}

		if (empty($smartcontract)){
			foreach ($default_smartcontracts as $default_smartcontract){
				$smartcontract = new SmartContracts;
				$smartcontract->id_user = Yii::$app->user->id;
				$smartcontract->id_blockchain = $default_smartcontract->id_blockchain;
				$smartcontract->id_contract_type = $default_smartcontract->id_contract_type;
				$smartcontract->denomination = $default_smartcontract->denomination;
				$smartcontract->smart_contract_address = $default_smartcontract->smart_contract_address;
				$smartcontract->decimals = $default_smartcontract->decimals;
				$smartcontract->symbol = $default_smartcontract->symbol;
				if (!$smartcontract->save()){
					var_dump( $smartcontract->getErrors());
					die();
				}
			}
		}
		// inserisco la blockchain INSERITA NEI PARAMS come default
		// Poi l'utente può successivamente cambiarla
		// in tal modo posso utilizzare lo stesso software in più
		// ambiti!
		if (null === $nodes){
			$nodes = new Nodes;
			$nodes->id_user = Yii::$app->user->id;
			$nodes->id_blockchain = Yii::$app->params['default_blockchain'];
			$nodes->id_smart_contract = Yii::$app->params['default_smartcontract'];
			if (!$nodes->save()){
				var_dump( $nodes->getErrors());
				die();
			}
		}
		//
		// $blockchain = Blockchains::find()->where(['id_user'=>Yii::$app->user->id])->all();
		// $smartcontract = SmartContracts::find()->where(['id_user'=>Yii::$app->user->id])->all();
		// $nodes = Nodes::find()->where(['id_user'=>Yii::$app->user->id])->all();

		// echo '<pre>'.print_r($blockchain,true);;
		// echo '<pre>'.print_r($smartcontract,true);;
		// echo '<pre>'.print_r($nodes,true);exit;
		//
		//
		//
		// die();

		return $nodes;
	}




}
