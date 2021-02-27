<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\PincodeAsset;
use app\assets\NotificationsAsset;
use app\assets\ServiceWorkerAsset;
use app\assets\SynchronizeBlockchainAsset;

use app\components\Settings;

function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}

// impostazioni variabili globali per tutti i js
$options = [
    'cryptedIdUser' => app\components\WebApp::encrypt(Yii::$app->user->id),
    'WebSocketServerAddress' => isLocalhost() ? 'ws://localhost:8080' : 'wss://wss.megawallet.tk/wss',
    // ...
];
$this->registerJs(
    "var yiiGlobalOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    yii\web\View::POS_HEAD,
    'yiiGlobalOptions'
);


AppAsset::register($this);

PincodeAsset::register($this);
NotificationsAsset::register($this);
ServiceWorkerAsset::register($this);
SynchronizeBlockchainAsset::register($this);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <meta name="google-signin-client_id" content="<?php echo Settings::load()->GoogleOauthClientId; ?>">

    <!-- Manifest Progressive Web App -->
    <link rel="manifest" href="manifest.json">

    <!-- Google font file. If you want you can change. -->
  	<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,900" rel="stylesheet">

    <!-- Fontawesome font file css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />

    <?php $this->head() ?>

    <!-- Template global css file. Requared all pages -->
  	<!-- <link rel="stylesheet" type="text/css" href="css/global.style.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="css/site.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="css/numpad.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="css/yiipager.css"> -->
</head>
<body>
<?php $this->beginBody() ?>



<div class="wrapper">
    <?php //echo $this->render('_sidebar'); ?>
    <?php echo $this->render('_navbar'); ?>
    <div class="wrapper-inline">
        <?php $this->beginContent('@app/views/layouts/base.php') ?>

        <?php echo $this->render('_searchform'); ?>

        <main class="margin mt-0">

            <?= Alert::widget() ?>
            <?= $content ?>
        </main>
        <?php $this->endContent() ?>
    </div>

</div>



<?php $this->endBody() ?>

</body>

<?php
// modal PAGE

if (Yii::$app->controller->id == 'users'){
    echo $this->render('_pin-manage');
    echo $this->render('_push-manage');
}

// if (Yii::$app->controller->id == 'send'){
//     //echo $this->render('_camera-request');
// }

echo $this->render('_pin-request');

?>

</html>
<?php $this->endPage() ?>
