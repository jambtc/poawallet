<?php
$secrets = require __DIR__ . '/secrets.php';

return [
    'adminEmail' => $secrets['mail_adminusername'],
    'senderEmail' => $secrets['mail_username'],
    'senderName' => $secrets['mail_name'],
    'logoApplicazione' => '/css/images/logo.png',
    'website' => 'www.txlab.it',
    'adminName' => 'txLab',
    'supportEmail' => $secrets['mail_username'],
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
    'telegram.clientId' => $secrets['telegram_client_id'],
    'telegram.clientSecret' => $secrets['telegram_client_secret'],

    // smart contract
    'smartcontract_address' => $secrets['smartcontract_address'],
    'default_blockchain' => $secrets['default_blockchain'],
    'default_smartcontract' => $secrets['default_smartcontract'],


    // websocker url
    'websocket_url' => $secrets['websocket_url'],

];
