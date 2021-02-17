<?php

$vapidPublicKey = \settings::load()->VapidPublic;
$urlSavesubscription = \yii\helpers\Url::to(['users/save-subscription']);//save subscription for push messages

$options = [
    'cryptURL' => \yii\helpers\Url::to(['/wallet/crypt']),
    'expiringTime' => 5, // in test altrimenti inserisci 5 minuti
    //'vapidPublicKey' => \settings::load()->VapidPublic,
    // 'urlSavesubscription' => \yii\helpers\Url::to(['wallet/saveSubscription']),//save subscription for push messages
    // ...
];
$this->registerJs(
    "var yiiOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    \yii\web\View::POS_HEAD,
    'yiiOptions'
);
