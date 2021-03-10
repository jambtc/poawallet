<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use \yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\Users;
use app\components\WebApp;

define ('NONCE_TIMEOUT', 24 * 60 * 60); // 1 day

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    // 'activate' => ['post'],
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

    public function beforeAction($action)
	{
    	$this->enableCsrfValidation = false;
    	return parent::beforeAction($action);
	}

    private static function setCookieForGoogleLogout()
    {
      setcookie('G_AUTHUSER_LOGOUT','AVOID');
    }

    public function actionError(){
        $this->layout = 'auth';
        return $this->render('error');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['wallet/index']);
        }

        $this->setCookieForGoogleLogout();


        return $this->render('index');
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // echo "<pre>".print_r($_POST,true)."</pre>";
		// exit;

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // return $this->goBack();
            return $this->redirect(['wallet/index']);
        }

        $this->setCookieForGoogleLogout();

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        // setcookie('tg_user', '');
        // setcookie('stel_ssid', '');
        // setcookie('stel_token', '');
        // setcookie('G_AUTHUSER_LOGOUT','TRUE');
        $this->setCookieForGoogleLogout();

        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    // public function actionContact()
    // {
    //     $model = new ContactForm();
    //     if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
    //         Yii::$app->session->setFlash('contactFormSubmitted');
    //
    //         return $this->refresh();
    //     }
    //     return $this->render('contact', [
    //         'model' => $model,
    //     ]);
    // }

    /**
     * Displays about page.
     *
     * @return string
     */
    // public function actionAbout()
    // {
    //     return $this->render('about');
    // }

    public function actionRegister()
    {
        $this->layout = 'auth';

        // echo "<pre>".print_r($_POST,true)."</pre>";
		// exit;

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('registerFormSubmitted');
            // return $this->refresh();
        }

        $model->password = '';
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionActivate()
    {
        $this->layout = 'auth';

        // echo "<pre>".print_r($_POST,true)."</pre>";
		// exit;
        $id = WebApp::decrypt($_GET['id']);
        // echo "<pre>".print_r($id,true)."</pre>";
        // exit;

        // check if the message is outdated
        $microtime = explode(' ', microtime());
        $nonce = $microtime[1] . str_pad(substr($microtime[0], 2, 6), 6, '0');
        $a = substr($nonce,1,9)*1;

        $user = $this->findModel($id);
        $b = substr($user->activation_code,1,9)*1;

        // echo "<pre>".print_r($a,true)."</pre>";
        // echo "<pre>".print_r($b,true)."</pre>";
        // echo "<pre>".print_r($a-$b,true)."</pre>";
        // echo "<pre>".print_r(NONCE_TIMEOUT,true)."</pre>";
        // exit;

        // if (($a - $b) > 1){
        if (($a - $b) > NONCE_TIMEOUT){
            // verifica che non sia attivo e lo cancella
            $delete = Users::find()
                ->andWhere(['id'=>$id])
                ->andWhere(['status_activation_code'=>0])
            ->one();
            $delete->delete();
            Yii::$app->session->setFlash('dataOutdated');
            // return $this->refresh();
        }
        // Now do the sign
        $sign = base64_encode(hash_hmac('sha512', hash('sha256', $user->activation_code . $user->accessToken, true), base64_decode($user->authKey), true));

        // echo "<pre>".print_r($sign,true)."</pre>";
        // echo "<pre>".print_r($_GET['sign'],true)."</pre>";


        // exit;


        // compare the two signatures
        if (strcmp($sign, $_GET['sign']) == 0){
            // echo "<pre>".print_r('sono uguali',true)."</pre>";
            $user->activation_code = '';
            $user->accessToken = '';
            $user->status_activation_code = 1;
            $user->save();
            // exit;
        }else{
            // echo "<pre>".print_r('sono diver',true)."</pre>";
            $delete = Users::find()
                ->andWhere(['id'=>$id])
                ->andWhere(['status_activation_code'=>0])
            ->one();
            $delete->delete();
            Yii::$app->session->setFlash('dataNotSigned');
        }
        // exit;


        return $this->render('activate', [
            'model' => $user,
        ]);

    }

    /**
     * Finds the Users model based on its user_id value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        // echo "<pre>".print_r($id,true)."</pre>";
		// exit;
        $model = Users::find()->andWhere(['id'=>$id])->one();
        if ( $model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested user does not exist.'));
    }


}
