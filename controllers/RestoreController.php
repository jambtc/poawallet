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

use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Url;


// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

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

		$formModel = new WizardWalletForm; //form di input dei dati

		if (Yii::$app->request->isAjax && $formModel->load(Yii::$app->request->post())) {
		    Yii::$app->response->format = Response::FORMAT_JSON;
			// echo '<pre>'.print_r(ActiveForm::validate($sendTokenForm),true).'</pre>';
		    return ActiveForm::validate($formModel);
		}

		if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
			// controllo se sono inseriti dei nodi all'interno del db
			$nodes = Nodes::findOne(1);

			if (null === $nodes){
				// entro nella richiesta di selezione o inserimento del nodo
				return $this->redirect(['/settings/blockchains/index']);
			}
			echo '<pre> [nodes]'.print_r($nodes,true);exit;




			// se sono giunto qui, l'indirizzo dell'utente non doveva essere in tabella
			// oppure non corrisponde a quello salvato in indexedDB
			$boltWallet = MPWallets::find()->where( [ 'id_user' => Yii::$app->user->id ] )->one();

			if(null === $boltWallet) {
			  	//doesn't exist so create record
			  	$ERC20 = new Yii::$app->Erc20(1); // blockchain id -> 1
				$boltWallet = new MPWallets;
				$boltWallet->id_user = Yii::$app->user->id;
				$block = $ERC20->getBlockInfo();


				$json = json_decode($block);

				if (!isset($json->error)){
					$boltWallet->blocknumber = ($block === null) ? '0x0' : $block->number;
				} else {
					$boltWallet->blocknumber = '0x0';

				}


				// echo '<pre>'.print_r($block,true);exit;


			}
			$boltWallet->wallet_address = Yii::$app->request->post('WizardWalletForm')['address'];

			if ($boltWallet->save())
        		return $this->redirect(['/wallet/index']);
			else
				var_dump( $boltWallet->getErrors());

			exit;
    	}

 		return $this->render('index', [
			'formModel' => $formModel,
		]);
 	}




}
