<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;

NavBar::begin([
  // 'brandLabel' => Yii::$app->name,
  'brandLabel' => Html::img('@web/css/images/logo.png', [
    'alt'=>Yii::$app->name,
    'style' => 'width: 45px; display: inline;',
    ]). '<span style="margin-left: 10px;">'.Yii::$app->name.'</span>',
  'brandUrl' => Yii::$app->homeUrl,
  'options' => [
    // 'class' => 'navbar-inverse navbar-fixed-top',
    'class' => 'navbar navbar-expand-md navbar-light shadow mb-0',
  ],
]);

if (Yii::$app->user->isGuest){
    $items = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Contact', 'url' => ['/site/contact']],
    ];
}else{
    $items = [
        ['label' => Yii::t('lang','Wallet'), 'url' => ['/site/index']],
        ['label' => Yii::t('lang','Transactions'), 'url' => ['/transactions/index']],
        ['label' => Yii::t('lang','Bug report'), 'url' => ['/site/contact-form']],
        ['label' => Yii::t('lang','Settings'), 'url' => ['/settings/index']],
        ['label' => Yii::t('lang','Profile'), 'url' => ['users/view']],
    ];

    $items[] = '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                  'Logout (' . Yii::$app->user->identity->first_name . ')',
                  ['class' => 'btn btn-link logout']
                  )
                  . Html::endForm()
                . '</li>';
}





echo Nav::widget([
  'options' => [
    'class' => 'navbar-nav navbar-right d-lg-none'
  ],
  'items' => $items,
]);

NavBar::end();
