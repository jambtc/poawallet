<?php

namespace app\controllers;

use Yii;
use app\models\BoltSocialusers;
use app\models\BoltSocialusersSearch;
use app\models\BoltWallets;
use app\models\BoltTokens;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';
Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';

/**
 * BoltSocialusersController implements the CRUD actions for BoltSocialusers model.
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


    /**
     * Displays a single BoltSocialusers model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $wallet = BoltWallets::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->id])
 	    		->one();

        $sent = round(BoltTokens::find()
            ->where(['from_address'=>$wallet->wallet_address])
            ->sum('token_price'), \settings::load()->poa_decimals);

        $received = round(BoltTokens::find()
            ->where(['to_address'=>$wallet->wallet_address])
            ->sum('token_price'), \settings::load()->poa_decimals);

        $transactions = BoltTokens::find()
            ->orwhere(['=','to_address', $wallet->wallet_address])
            ->orwhere(['=','from_address', $wallet->wallet_address])->count();


        return $this->render('view', [
            'model' => $this->findModel(\webapp::decrypt($id)),
            'sent' => $sent,
            'received' => $received,
            'transactions' => $transactions,
        ]);
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
        $model = $this->findModel(\webapp::decrypt($id));

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
     * @return BoltSocialusers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = BoltSocialusers::find()->andWhere(['id_user'=>$id])->one();
        if ( $model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
