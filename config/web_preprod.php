<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/preprod/db.php';

$config = [
    'name' => 'LFV-PreProd',
    'id' => 'LFV-PreProd',
    'language' => 'de-DE',
    'basePath' => dirname(__DIR__),
    
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '4FcHbc1q2GPzCgyWcVJuov5uVMSGGvwp',
            //'scriptUrl' => '/web/index-preprod.php', // Base Script to process requests
        ],
        
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        /*
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],*/
        
        // User Class for RBAC
        
        'user' => [
            'identityClass' => 'mdm\admin\models\User',
            'enableAutoLogin' => false,
            'loginUrl' => ['admin/user/login'],
        ],
        
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.office365.com',
                'username' => 'bn@htlwy.at',
                'password' => 'DHbsi3340V.',
                'port' => '587',
                'encryption' => 'tls',
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
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                    'sourceLanguage' => 'de-de',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'formatter' => [
            'nullDisplay' => "",    // Standardwert bei leerem Feld
        ],
        'urlManager' => [
            'enablePrettyUrl' => false, // BN: true does not work on production server
            'showScriptName' => false,
            'scriptUrl' => "index-preprod.php",
            'enableStrictParsing' => false,
            'rules' => [
                // ...
            ],
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => false,
        ],
        */
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            //'defaultRoles' => ['guest']
        ],
        
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'site/about',
            'site/logout',
            //'admin/user/signup',
            'admin/user/login',
            'admin/user/request-password-reset',
            //'admin/route',
            //'*',
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            //'layout' => 'left-menu', // avaliable value 'left-menu', 'right-menu' and 'top-menu'
            //'mainLayout' => '@app/views/layouts/main.php',
           
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
            // your other grid module settings
        ],
       'gridviewKrajee' =>  [
            'class' => '\kartik\grid\Module',
            // your other grid module settings
        ]
    ],
    'params' => $params,
];

//if (YII_ENV_DEV) {
if(true){
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];
}

return $config;
