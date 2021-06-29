<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\MPWallets;
use app\models\Transactions;
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

        $node = Settings::poa(Yii::$app->user->id);

        $tokens_from = Transactions::find()
            ->andWhere(['id_smart_contract'=>$node->smartContract->id])
            ->andWhere(['from_address'=>$wallet->wallet_address])
            ->andWhere(['status'=>'complete']);

        $tokens_to = Transactions::find()
            ->andWhere(['id_smart_contract'=>$node->smartContract->id])
            ->andWhere(['to_address'=>$wallet->wallet_address])
            ->andWhere(['status'=>'complete']);

        $sent['sum'] = round($tokens_from->sum('token_price'), $node->smartContract->decimals);
        $sent['count'] = round($tokens_from->count(), $node->smartContract->decimals);

        $received['sum'] = round($tokens_to->sum('token_price'), $node->smartContract->decimals);
        $received['count'] = round($tokens_to->count(), $node->smartContract->decimals);

        $total_transactions = $sent['count'] + $received['count'];

        // genero array per il grafico
        $chart = Transactions::find()
            ->orwhere(['=','from_address', $wallet->wallet_address])
            ->orwhere(['=','to_address', $wallet->wallet_address])
            ->andWhere(['status'=>'complete'])
            ->andWhere(['id_smart_contract'=>$node->smartContract->id])
            ->select('token_price, from_address, to_address')
            ->all();

        // echo '<pre>'.print_r($wallet->wallet_address,true);exit;

        $userAccountValues = [0.01];
        $increase = 0;
        $color = 'gray';
        $arrow = 'arrows-alt-v';
        if (count ($chart) > 0){
            $actualPrice = 0;
            $symbol = '';
            foreach ($chart as $item){
                // echo '<pre>'.print_r($item,true);exit;

                if ($item->from_address == $wallet->wallet_address){
                    $actualPrice -= $item['token_price'];
                } else {
                    $actualPrice += $item['token_price'];
                }

                $userAccountValues[] = $actualPrice;

            }

            // echo count($userAccountValues);
            // exit;
            $last1 = $userAccountValues[count($userAccountValues)-1];
            $last2 = $userAccountValues[count($userAccountValues)-2];

            if ($last1 > $last2){
                $increase = round($last1 / $last2 *100, 2);
                $color = 'green';
                $arrow = 'up';
            } else {
                $increase = round($last2 / $last1 *100, 2);
                $color = 'red';
                $arrow = 'down';
            }
        }



        $userAccountValueArray = [
            'accountValues' => $userAccountValues,
            'balance' => $received['sum'] - $sent['sum'],
            'increase' => $increase,
            'color' => $color,
            'arrow' => $arrow,
        ];


        // echo '<pre>'.print_r($userAccountValueArray,true);exit;

        return $this->render('view', [
            'model' => $this->findModel(WebApp::decrypt($id)),
            'sent' => $sent,
            'received' => $received,
            'transactions' => $total_transactions,
            'percent_sent' => $sent['count'] / (1+$total_transactions) * 100,
            'percent_received' => $received['count'] / (1+$total_transactions) * 100,
            'userAccountValueArray' => $userAccountValueArray,
            'node' => $node,
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
