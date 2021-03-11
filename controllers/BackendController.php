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


use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\Html;

use app\components\WebApp;

// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';



class BackendController extends Controller
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
					'notify',
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
							'notify',
							'updateAllNews',
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

	// aggiorna solo la notifica in "letta"
	// update one row
	public function actionUpdateSingleNews(){
		// UPDATE `customer` SET `status` = 1 WHERE `email` LIKE `%@example.com%`
		$update = NotificationsReaders::updateAll(
			[
				'alreadyread' => NotificationsReaders::STATUS_READ
			],
			[
				'like', 'id_notification', $_POST['id_notification'],
			]
		);

		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['success'=>true,'response'=>$update];
	}

	// aggiorna tutte le notifiche in "letta"
	// update all rows
	public function actionUpdateAllNews(){
		// UPDATE `customer` SET `status` = 1 WHERE `email` LIKE `%@example.com%`
		$update = NotificationsReaders::updateAll(
			[
				'alreadyread' => NotificationsReaders::STATUS_READ
			],
			[
				'like', 'id_user', Yii::$app->user->id
			]
		);

		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['success'=>true,'response'=>$update];
	}

}
