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
use app\models\BoltWallets;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Url;


Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

class RestoreController extends Controller
{

	public function beforeAction($action)
	{
    	$this->enableCsrfValidation = false;
		$session = Yii::$app->session;
		$token = $session->get('token-restore');
		if ($token === null || $token != $_GET['token']) {
			Yii::$app->response->statusCode = 403;
			return false;
		}
		$session->remove('token-restore');

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
			// salvo l'indirizzo in tabella
			$boltWallet = new BoltWallets;
			$boltWallet->id_user = Yii::$app->user->identity->id;
			$boltWallet->wallet_address = Yii::$app->request->post('WizardWalletForm')['address'];
			$boltWallet->blocknumber = '0x0';

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

	private static function json ($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $data;
	}








}
