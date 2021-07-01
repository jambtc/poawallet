<?php
$secrets = require __DIR__ . '/secrets.php';

// in secret file you must put the correct info for the correct domain
// 'oauth_telegram' => [
//     'client_id' => [
//         'localhost' => 'aaa'
//         'exmpale.com' => 'xxxxx',
//     ],
//     'secret' => [
//         'localhost' => 'bbbbbbb'
//         'axample.com' => 'yyyyyyy',
//     ]
// ],

if (isset($_SERVER['SERVER_NAME'])){
    $telegramClientId = $secrets['oauth_telegram']['client_id'][$_SERVER['SERVER_NAME']] ?? null;
    $telegramClientSecret = $secrets['oauth_telegram']['secret'][$_SERVER['SERVER_NAME']] ?? null;
} else {
    $telegramClientId = null;
    $telegramClientSecret = null;
}



return [
    'adminEmail' => $secrets['mail_adminusername'],
    'senderEmail' => $secrets['mail_username'],
    'senderName' => $secrets['mail_name'],
    'logoApplicazione' => '/css/images/logo.png',
    'website' => $secrets['website'],
    'adminName' => $secrets['adminName'],
    'supportEmail' => $secrets['mail_support'],
    'encryptionFile' => dirname(__FILE__).'/encrypt.json',

    /**
     * Set the password reset token expiration time.
     */
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,

    /**
     * Set the list of usernames that we do not want to allow to users to take upon registration or profile change.
     */
    'user.spamNames' => 'admin|superadmin|creator|thecreator|username|administrator|root',

    //
    'user.rememberMeDuration' => 7776000, // This number is 60sec * 60min * 24h * 90days


    // Telegram login informations
    'telegram.clientId' => $telegramClientId,
    'telegram.clientSecret' => $telegramClientSecret,


    // smart contract
    'smartcontract_address' => $secrets['smartcontract_address'],
    'default_blockchain' => $secrets['default_blockchain'],
    'default_smartcontract' => $secrets['default_smartcontract'],

    // sealer address for POA CZN
    'sealer_account_2' => $secrets['sealer_account_2'],
    'sealer_prvkey_2' => $secrets['sealer_prvkey_2'],

    // sealer address for POA FID
    'sealer_account_3' => $secrets['sealer_account_3'],
    'sealer_prvkey_3' => $secrets['sealer_prvkey_3'],


    // websocker url
    // 'websocket_url' => $secrets['websocket_url'],

    // infura client
    'infura_client' => $secrets['infura_client'],
    'sealers' => $secrets['sealers'],
    'my_address' => $secrets['my_address'],
];
