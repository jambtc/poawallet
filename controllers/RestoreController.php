<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;

use yii\base\Model;
use yii\db\ActiveRecord;

use app\models\WizardWalletForm;
use app\models\MPWallets;
use app\models\Nodes;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Url;

use app\components\Networks;

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
					$node = Networks::createDefaults();
				}
				$boltWallet->blocknumber = $this->getLatestBlockNumber();
			}
			$boltWallet->wallet_address = Yii::$app->request->post('WizardWalletForm')['address'];

			if ($boltWallet->save()){
				if (null === $node && $defaultNetworkExist === false){
					Networks::createDefaults();
				}

				return $this->redirect(['/wallet/index']);
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
		$json = null;
		if (!is_object($block)){
			$json = json_decode($block);
		}

		if (!isset($json->error)){
			return ($block === null) ? '0x0' : $block->number;
		} else {
			return '0x0';
		}
	}

}
