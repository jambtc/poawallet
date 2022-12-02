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
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\components\WebApp;
use app\components\AuthHandler;

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
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ]
        ];
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

    public function beforeAction($action)
	{
    	$this->enableCsrfValidation = false;
    	return parent::beforeAction($action);
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
        Yii::$app->user->logout();
        return $this->goHome();
    }

    
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $this->layout = 'auth';


        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('registerFormSubmitted');
        }

        $model->password = '';
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Activate registered user.
     *
     * @return Response|string
     */
    public function actionActivate($id, $sign)
    {
        $this->layout = 'auth';
        $id_decrypted = WebApp::decrypt($id);

        // check if the message is outdated
        $microtime = explode(' ', microtime());
        $nonce = $microtime[1] . str_pad(substr($microtime[0], 2, 6), 6, '0');
        $a = substr($nonce, 1, 9) * 1;

        $user = Users::findOne($id_decrypted);
        
        if (null !== $user){
            $b = substr($user->activation_code, 1, 9) * 1;
    
            $diff = $a - $b;
            if ($diff > Yii::$app->params['nonce.timeout']) {
                // verifica che non sia attivo e lo cancella
                if ($user->status_activation_code == 0) {
                    $user->delete();
                    Yii::$app->session->setFlash('registerError', Yii::t('app', '<strong>Error!</strong> The registration time has expired. You have to register again!'));
                }
            }
            // Now do the sign
            $signature = base64_encode(hash_hmac('sha512', hash('sha256', $user->activation_code . $user->accessToken, true), base64_decode($user->authKey), true));
    
            // compare the two signatures
            if (strcmp($signature, $sign) == 0) {
                // echo "<pre>".print_r('sono uguali',true)."</pre>";exit;
                $user->activation_code = '000';
                $user->accessToken = '000';
                $user->status_activation_code = Users::STATUS_ACTIVE;
                $user->save();
                Yii::$app->session->setFlash('userActived', Yii::t('app', 'You have registered your account successfully.'));
                // exit;
            } else {
                $user->delete();
                Yii::$app->session->setFlash('registerError', Yii::t('app', '<strong>Error!</strong> The registration time has expired. You have to register again!'));
            }

        } else {
            Yii::$app->session->setFlash('registerError', Yii::t('app', '<strong>Error!</strong> Your account doesn\'t exist. You have to register again!'));
        }
        // exit;
        return $this->render('activate', [
            'model' => $user,
        ]);
    }

    /**
    * Requests password reset.
    *
    * @return mixed
    */
   public function actionRequestPasswordReset()
   {
       $this->layout = 'auth';

       $model = new PasswordResetRequestForm();
       if ($model->load(Yii::$app->request->post()) && $model->validate()) {
           if ($model->sendEmail()) {
               Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

               return $this->goHome();
           } else {
               Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
           }
       }

       return $this->render('requestPasswordResetToken', [
           'model' => $model,
       ]);
   }

   /**
    * Resets password.
    *
    * @param string $token
    * @return mixed
    * @throws BadRequestHttpException
    */
   public function actionResetPassword($token)
   {
       $this->layout = 'auth';

       try {
           $model = new ResetPasswordForm($token);
       } catch (InvalidArgumentException $e) {
           throw new BadRequestHttpException($e->getMessage());
       }

       if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
           Yii::$app->session->setFlash('success', Yii::t('app','New password saved.'));

           return $this->goHome();
       }

       return $this->render('resetPassword', [
           'model' => $model,
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
