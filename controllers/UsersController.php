<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\MPWallets;
use app\models\BoltTokens;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\HttpException;

use app\models\PushSubscriptions;

use yii\helpers\Json;
use yii\helpers\Url;

use app\components\WebApp;
use app\components\Settings;


// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';
// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    private static function json ($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $data;
	}


    /**
     * Displays a single BoltSocialusers model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $wallet = MPWallets::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->id])
 	    		->one();

        $sent = round(BoltTokens::find()
            ->where(['from_address'=>$wallet->wallet_address])
            ->sum('token_price'), Settings::load()->poa_decimals);

        $received = round(BoltTokens::find()
            ->where(['to_address'=>$wallet->wallet_address])
            ->sum('token_price'), Settings::load()->poa_decimals);

        // $transactions = BoltTokens::find()
        //     ->orwhere(['=','to_address', $wallet->wallet_address])
        //     ->orwhere(['=','from_address', $wallet->wallet_address])->count();


        return $this->render('view', [
            'model' => $this->findModel(WebApp::decrypt($id)),
            'sent' => $sent,
            'received' => $received,
            // 'transactions' => $transactions,
        ]);
    }

    /**
	 * Saves the Subscription for push messages.
	 * @param POST VAPID KEYS
	 * this function NOT REQUIRE user to login
	 */
	public function actionSaveSubscription()
	{
 		$raw = json_decode($_POST['subscription']);
        // echo var_dump ($raw);
		$browser = $_SERVER['HTTP_USER_AGENT'];

		$vapid = PushSubscriptions::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->identity->id])
				->andWhere(['browser'=>$browser])
				->andWhere(['type'=>'wallet'])
 	    		->one();

		if (null === $vapid){
			//save
			$vapid = new PushSubscriptions;
			$vapid->id_user = Yii::$app->user->identity->id;
			$vapid->browser = $browser;
			$vapid->endpoint = $raw->endpoint;
			$vapid->auth = $raw->keys->auth;
			$vapid->p256dh = $raw->keys->p256dh;
			$vapid->type = 'wallet';

			if (!$vapid->save()){
				$data = ['response' => '[WalletController] SaveSubscription: Cannot save subscription on server!'];
			} else {
				$data = ['response' => '[WalletController] SaveSubscription: Subscription saved on server.'];
			}
		}else{
			if (!$vapid->delete()){
				$data = ['response' => '[WalletController] SaveSubscription: Cannot delete subscription on server!'];
			} else {
				$data = ['response' => '[WalletController] SaveSubscription: Subscription deleted on server.'];
			}
		}
		return $this->json($data);
	}



    /**
     * Updates an existing BoltSocialusers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(WebApp::decrypt($id));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_social]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Finds the BoltSocialusers model based on its user_id value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Users::find()->andWhere(['id'=>$id])->one();
        if ( $model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
