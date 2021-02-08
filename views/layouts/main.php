<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

Yii::$classMap['logo'] = Yii::getAlias('@packages').'/logo.php';
Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';

AppAsset::register($this);


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

    <meta name="google-signin-client_id" content="<?php echo \settings::load()->GoogleOauthClientId; ?>">

    <!-- Manifest Progressive Web App -->
    <link rel="manifest" href="manifest.json">

    <!-- Google font file. If you want you can change. -->
  	<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,900" rel="stylesheet">

    <!-- Fontawesome font file css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />

    <?php $this->head() ?>

    <!-- Template global css file. Requared all pages -->
  	<link rel="stylesheet" type="text/css" href="css/global.style.css">
    <link rel="stylesheet" type="text/css" href="css/site.css">
</head>
<body>
<?php $this->beginBody() ?>
<?php $this->beginContent('@app/views/layouts/base.php') ?>


<main class="">
    <?php echo $this->render('_sidebar'); ?>
    <div class="content-wrapper">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>




<?php $this->endContent() ?>


<?php $this->endBody() ?>
<!-- Template global script file. requared all pages -->
<script src="js/global.script.js"></script>

<!-- Call Service Worker-->
<script src="src/js/promise.js"></script>
<script src="src/js/fetch.js"></script>
<script src="src/js/idb.js"></script>

<!-- my utility js -->
<script src="src/js/utility.js"></script>
<script src="src/js/service.js"></script>


</body>
</html>
<?php $this->endPage() ?>
