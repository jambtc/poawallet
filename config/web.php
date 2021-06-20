<?php
$secrets = require __DIR__ . '/secrets.php';

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

// in secret file you must put the correct info for the correct domain
// 'oauth_github' => [
//     'client_id' => [
//         'localhost' => 'aaa'
//         'exmpale.com' => 'xxxxx',
//     ],
//     'secret' => [
//         'localhost' => 'bbbbbbb'
//         'axample.com' => 'yyyyyyy',
//     ]
// ],

$config = [
    'id' => 'Poa Wallet',
    'name' => 'Poa Wallet',
    'language' => 'en-US', // Specifies which language the application is targeted to
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'Erc20' => ['class' => 'app\components\Erc20'],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => $secrets['cookieValidationKey'],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true, //
            'authTimeout' => 7776000,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'authClientCollection' => [
           'class' => 'yii\authclient\Collection',
           'clients' => [
               'google' => [
                   'class' => 'yii\authclient\clients\Google',
                   'clientId' => $secrets['google_client_id'],
                   'clientSecret' => $secrets['google_client_secret'],
               ],
               'facebook' => [
                   'class' => 'yii\authclient\clients\Facebook',
                   // 'validateAuthState' => false, //TEMPORARY
                   'attributeNames' => ['id', 'email', 'first_name', 'last_name'],
                   'clientId' => $secrets['facebook_client_id'],
                   'clientSecret' => $secrets['facebook_client_secret'],
                    // 'attributeParams' => [
                    //     'include_email' => 'true'
                    // ],
               ],
               'github' => [
                   'class' => 'yii\authclient\clients\GitHub',
                   'clientId' => $secrets['oauth_github']['client_id'][$_SERVER['SERVER_NAME']],
                   'clientSecret' => $secrets['oauth_github']['secret'][$_SERVER['SERVER_NAME']],
               ],
               // 'twitter' => [
               //      'class' => 'yii\authclient\clients\Twitter',
               //      'attributeParams' => [
               //          'include_email' => 'true'
               //      ],
               //      'consumerKey' => $secrets['twitter_consumer_key'],
               //      'consumerSecret' => $secrets['twitter_consumer_secret'],
               //  ],
               // etc.
           ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => $secrets['useFileTransport'],
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $secrets['mail_host'],
                'username' => $secrets['mail_username'],
                'password' => $secrets['mail_password'],
                'port' => $secrets['mail_port'],
                'encryption' => $secrets['encryption'],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        //this component manage the url creation
        // 'urlManager' => [
        //     'enablePrettyUrl' => true,
        //     'showScriptName' => false,
        //     // 'rules' => [
        //     //
        //     //
        //     // ],
        // ],

        // this component add timestamp to downloaded css or javascript
        // to avoid forced refresh during develop
        //
        'assetManager' => [
            'appendTimestamp' => true
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
