<?php

namespace app\controllers;

use Yii;
use app\models\Transactions;
use app\models\search\TransactionsSearch;
use app\models\MPWallets;
use app\models\Nodes;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

use app\components\WebApp;

/**
 * TransactionsController implements the CRUD actions for Transactions model.
 */
class TokensController extends Controller
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
     * Lists all Transactions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $fromAddress = MPWallets::find()->userAddress(Yii::$app->user->id);
        $node = Nodes::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->id])
 	    		->one();

		if (NULL === $fromAddress || NULL === $node){
			$session = Yii::$app->session;
			$string = Yii::$app->security->generateRandomString(32);
			$session->set('token-wizard', $string );
			return $this->redirect(['wizard/index','token' => $string]);
		}

        $searchModel = new TransactionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(['pageSize' => 10]);
		$dataProvider->sort->defaultOrder = ['invoice_timestamp' => SORT_DESC];
		$dataProvider->query
					->orwhere(['=','to_address', $fromAddress])
					->orwhere(['=','from_address', $fromAddress]);

        $dataProvider->query->andwhere(['=','id_smart_contract', $node->id_smart_contract]);
        // echo '<pre>'.print_r($dataProvider,true);exit;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'fromAddress' => $fromAddress,
        ]);
    }

    /**
     * Displays a single Transactions model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel(WebApp::decrypt($id)),
        ]);
    }

    /**
     * Displays a single Transactions model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetTransactionDetails($txhash)
    {
        $receipt = '';
        $success = false;

        $node = Nodes::find()
 	     		->andWhere(['id_user'=>Yii::$app->user->id])
 	    		->one();

		$ERC20 = new Yii::$app->Erc20();

        if ($txhash != '0x0'){
            $success = true;
            $receipt = $ERC20->getReceipt($txhash);

            // update transactions
            $transaction = Transactions::find()
                ->findByHash($txhash);

            $transactionValue = $ERC20->wei2eth(
                $receipt->logs[0]->data,
                $node->smartContract->decimals
            );

            // echo "<pre>".print_r($transactionValue,true)."</pre>";
            // echo "<pre>".print_r($receipt,true)."</pre>";
            // echo "<pre>".print_r($transaction,true)."</pre>";exit;

            $transaction->blocknumber = $receipt->blockNumber;
            $transaction->token_received = $transactionValue;
            $transaction->status = 'complete';
            if (!$transaction->save()){
                var_dump( $transaction->getErrors());
                die();
            }

        }
        $return = [
            'success' => $success,
            'receipt' => $receipt,
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }




    /**
     * Finds the Transactions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transactions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transactions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
