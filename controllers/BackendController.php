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
use app\models\MPWallets;


use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\Html;

use app\components\WebApp;


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
	public function actionNotify()
	{
		// echo "<pre>".print_r($_POST,true)."</pre>";
		// exit;
		$fromAddress = MPWallets::find()->userAddress(Yii::$app->user->id);

		$news = NotificationsReaders::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->id])
				->latest()
 	    		->all();

				// echo "<pre>".print_r($news,true)."</pre>";
				// exit;

	   $response['countedRead'] = 0;
	   $response['countedUnread'] = 0;
	   $response['htmlTitle'] = '';
	   $response['htmlContent'] = ''; // ex content
	   $response['playSound'] = false;
	   $response['playAlarm'] = false;

	   foreach ($news as $key => $item) {
		   ($item->alreadyread == 0 ? $response['countedUnread'] ++ : $response['countedRead'] ++);
	   }


	   $x=1;
	   foreach ($news as $key => $item) {
		   // echo "<pre>".print_r($item,true)."</pre>";
		   // exit;

		   if ($x == 1){

			   $response['htmlTitle'] .= '<li>
		 			 <div class="d-flex align-items-center justify-content-between">
		 				 <div class="d-flex align-items-center">
		 				   <div class="coin-name notify-htmlTitle">'
					   . Html::encode(\Yii::t('app',
						   'You have {n,plural,=0{read all messages.} =1{one unread message.} other{# unread messages.}}', ['n' => $response['countedUnread']]
					   ))
					   .'</div>

		 				 </div>
		 				 <div class="notify-readAll">
		 				   <a href="#" onclick="notify.openAllEnvelopes();"><small class="text-muted d-block">'. Yii::t('app','Mark all as read') .'</small></a>
		 				 </div>
		 			   </div>
		 		 </li>';
		   }
		   // Leggo la notifica tramite key
		   $notify = Notifications::findOne($item->id_notification);

		   //if ($data->from_address == $fromAddress){

		   //$notify = Notifications::model()->findByPk($item->id_notification);
		   $notifi__icon = WebApp::Icon($notify->type.'err');
		   $notifi__color = WebApp::Color($notify->status);

		   // verifico che sia un allarme
		   if ($notify->type == 'alarm' && $item->alreadyread == 0)
			   $response['playAlarm'] = true;


			$parsedurl = parse_url($notify->url);

			$classUnread = '';
			if ($item->alreadyread == 0) {
				$classUnread = 'bg-secondary-light';
			}

			$response['htmlContent'] .= '<li class='.$classUnread.'>
			<a onclick="notify.openEnvelope('.$notify->id.');"
				href="'.htmlentities('index.php?'.$parsedurl['query']).'"
				id="news_'.$notify->id.'">
	   			<div class="d-flex justify-content-between">

	                       <div class="p-2 text-info n otice-icon av ailable" st yle="min-width:30px;">
	                           <i class="'.$notifi__icon.'"></i>
	                       </div>

	                       <div class="mr-auto p-2">
	                         <p class="coin-name">'.Yii::t('app',$notify->description).'</p>
						   </div>
				</div>

							<div class="text-right">';
							 // se il tipo notifica è help o contact ovviamente non mostro il prezzo della transazione
							 // if ($notify->type <> 'help'
								// 	 && $notify->type <> 'contact'
								// 	 && $notify->type <> 'alarm'
							 // ){
								//  $response['htmlContent'] .= '<b class="d-block mb-0 float-left txt-dark">'.$notify->price.'</b>';
								//  //VERIFICO QUESTE ULTIME 3 TRANSAZIONI PER AGGIORNARE IN REAL-TIME LO STATO (IN CASO CI SI TROVA SULLA PAGINA TRANSACTIONS)
								//  // $response['status'][$notify->id_tocheck] = $notify->status;
							 // }
							 	$response['htmlContent'] .= '
								 <small class="text-muted">'.Yii::$app->formatter->asRelativeTime($notify->timestamp).'</small>
							 </div>



	               </div>
			   </a>
	   		</li>';


		   $x++;
		   if ($x>5)
			   break;
	   }
	   if ($response['countedRead'] == 0 && $response['countedUnread'] == 0){
		   $response['htmlContent'] .= '<div class="notifi__title">';
		   $response['htmlContent'] .= '<p>' . Yii::t('app','You have no messages to read.') . '</p>';
		   $response['htmlContent'] .= '</div>';
	   }else{
		   // $response['htmlContent'] .= '
			//    <div class="notifi__footer">
			// 	   <a id="seeAllMessages" onclick="backend.openAllEnvelopes();" href="'.htmlentities(Url::to(['messages/index'])).'">'.Yii::t('app','See all messages').'</a>
			//    </div>
		   // ';
		   $response['htmlContent'] .= '<li>
   			<div class="d-flex align-items-center justify-content-between">
                   <div class="d-flex align-items-center">
                       <a href="'.htmlentities(Url::to(['messages/index'])).'" class="text-muted">'.Yii::t('app','Manage notifications').'</a>
                   </div>
               </div>
   		</li>';
	   }

	   // $response['url'] = parse_url($notify->url);


	   Yii::$app->response->format = Response::FORMAT_JSON;
	   return $response;

	}






}
