<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

Yii::$classMap['logo'] = Yii::getAlias('@packages').'/logo.php';

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

    <!-- Google font file. If you want you can change. -->
  	<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,900" rel="stylesheet">

    <!-- Fontawesome font file css -->
  	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">

    <?php $this->head() ?>

    <!-- Template global css file. Requared all pages -->
  	<link rel="stylesheet" type="text/css" href="css/global.style.css">
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php

    if (Yii::$app->user->getIsGuest()) {
      NavBar::begin([
        // 'brandLabel' => Yii::$app->name,
        'brandLabel' => Html::img('@web/css/images/logo.png', [
          'alt'=>Yii::$app->name,
          'style' => 'width: 45px; margin-top: -11px; display: inline;',
          ]). '<span style="margin-left: 10px;">'.Yii::$app->name.'</span>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
          'class' => 'navbar-inverse navbar-fixed-top',
        ],
      ]);
      echo Nav::widget([
        'options' => [
          'class' => 'navbar-nav navbar-right'
        ],
        'items' => [
          ['label' => 'Home', 'url' => ['/site/index']],
          ['label' => 'About', 'url' => ['/site/about']],
          ['label' => 'Contact', 'url' => ['/site/contact']],
          // Yii::$app->user->isGuest ? (
          //   ['label' => 'Login', 'url' => ['/site/login']]
          //   ) : (
          //     '<li>'
          //     . Html::beginForm(['/site/logout'], 'post')
          //     . Html::submitButton(
          //       'Logout (' . Yii::$app->user->identity->username . ')',
          //       ['class' => 'btn btn-link logout']
          //       )
          //       . Html::endForm()
          //       . '</li>'
          //       )
              ],
            ]);
            NavBar::end();
    } else {
      include 'navmenu.php';
    }

    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
<?php
// echo  Yii::getAlias('@packages');
 // echo packages\logo\footer();
 // echo Yii::$app->logo->footer(); // cdt - CustomDateTime
 echo logo::footer();
?>
        <!-- <p class="pull-left"></p> -->

        <!-- <p class="pull-right"></p> -->
    </div>
</footer>

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
