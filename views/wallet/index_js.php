<?php

use yii\helpers\Url;
use yii\web\View;
// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

// make restore session id
$session = Yii::$app->session;
$string = Yii::$app->security->generateRandomString(32);
$session->set('token-wizard', $string );

$options = [
    'language' => Yii::$app->language,
    'initURL' => Url::to(['/wizard/index','token' => $string]),
    'fromAddress' => $fromAddress,
    'cryptedIdUser' => app\components\WebApp::encrypt(Yii::$app->user->identity->id),
    // ...
];
$this->registerJs(
    "var yiiOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    View::POS_HEAD,
    'yiiOptions'
);



$wallet_init = <<<JS
$(function(){
    /*
     * FUNZIONE INIZIALE DI INIZIALIZZAZIONE DEL WALLET
    */
    //LEGGO LE INFORMAZIONI DEL WALLET DA IndexedDB
    var isEquel = null;
    var my_address;
    //var isEquel;
    readFromId('wallet',yiiOptions.fromAddress)
        .then(function(data) {
            console.log('[Wallet IndexedDB]',data);
            // if (typeof data[0] !== 'undefined') {
            if (typeof data[0] !== 'undefined') {
                for (var dt of data) {
                    if (null === dt.id){
                        console.log('[Wallet IndexedDB]: 1');
                        window.location.href = yiiOptions.initURL;
                        break;
                    }else{
                        var address_1 = new String(yiiOptions.fromAddress);
                        var address_2 = new String(dt.id);

                        var iduser_1 = new String(yiiOptions.cryptedIdUser);
                        var iduser_2 = new String(dt.id_user);

                        isEquel_1 = JSON.stringify(address_1) === JSON.stringify(address_2);
                        console.log('[Wallet isEquel_1]',isEquel_1);

                        isEquel_2 = JSON.stringify(iduser_1) === JSON.stringify(iduser_2);
                        console.log('[idUSer isEquel_2]',isEquel_2);

                        console.log('[Wallet Mysql / IndexedDB]',address_1,address_2);
                        console.log('[idUSer Mysql / IndexedDB]',iduser_1,iduser_2);

                        isEquel = isEquel_1 * isEquel_2;
                        console.log('[isEquel]',isEquel);
                    }
                    // console.log('[Wallet Mysql / IndexedDB]',address_1,address_2);
                    if ( isEquel ){
                        /*  START 	*/
                        my_address = data[0].id;
                        console.log('[Wallet Address recuperato]', my_address);
                        break;
                    }else{
                        console.log('[Wallet IndexedDB]: 2');
                        window.location.href = yiiOptions.initURL;
                    }

                }
            }else{
                console.log('[Wallet IndexedDB]: 3');
                window.location.href = yiiOptions.initURL;

            }
        })

        .then(function() {
            console.log('[is isEquel true? ',isEquel);
            if (!isEquel){
                console.log('[Wallet IndexedDB]: 4');
                window.location.href = yiiOptions.initURL;
            }
        })
        .catch(function(err) {
            //se c'è un errore, probabilmente non esiste il db wallet pertanto inizializzo
            if (!isEquel){
                console.log('5');
                window.location.href = yiiOptions.initURL;
            }
        });
});



JS;

$this->registerJs(
    $wallet_init,
    View::POS_READY, //POS_END
    'wallet_restore'
);
