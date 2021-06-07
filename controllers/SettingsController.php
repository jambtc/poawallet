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
class SettingsController extends Controller
{

    /**
     * Displays a single BoltSocialusers model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionIndex()
    {
        return $this->render('index');

    }


}
