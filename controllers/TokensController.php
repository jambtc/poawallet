<?php

namespace app\controllers;

use Yii;
use app\models\BoltTokens;
use app\models\search\BoltTokensSearch;
use app\models\BoltWallets;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';
use app\components\WebApp;

/**
 * BoltTokensController implements the CRUD actions for BoltTokens model.
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
     * Lists all BoltTokens models.
     * @return mixed
     */
    public function actionIndex()
    {
        $fromAddress = BoltWallets::find()->userAddress(Yii::$app->user->id);
        if (null === $fromAddress){
			$session = Yii::$app->session;
			$string = Yii::$app->security->generateRandomString(32);
			$session->set('token-wizard', $string );
			$this->redirect(['wallet/wizard','token' => $string]);
		}

        $searchModel = new BoltTokensSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(['pageSize' => 10]);
		$dataProvider->sort->defaultOrder = ['invoice_timestamp' => SORT_DESC];
		$dataProvider->query
					->orwhere(['=','to_address', $fromAddress])
					->orwhere(['=','from_address', $fromAddress]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'fromAddress' => $fromAddress,
        ]);
    }

    /**
     * Displays a single BoltTokens model.
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
     * Finds the BoltTokens model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BoltTokens the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BoltTokens::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
