<?php
use kartik\mpdf\Pdf;
$params = require(__DIR__ . '/params.php');
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],

    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'weua5JjC3kcnaGJQhiMJUxqLhWftNa_Z',
            'parsers' => [
             'application/json' => 'yii\web\JsonParser',
         ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'enableSession' => true,
            'authTimeout' => 300,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

                '<controller:\w+>/<id:\d+>' => '<controller>/view',

                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',

                '<controller:\w+>/<action:\w+>' => '<controller>/<action>'
            ],
        ],
        'formatter'  => [
            'class' => 'yii\i18n\Formatter',
            'timeZone'        => 'Africa/Dar_es_Salaam',
        ],
    'as corsFilter' => [
        'class' => \yii\filters\Cors::class,
        'cors' => [
            'Origin' => ['*'],
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
        ],
    ],
        'pdf' => [
            'class' => Pdf::classname(),
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            ],
            'authManager' => [
                'class' => 'yii\rbac\DbManager',
                'defaultRoles' => ['guest', 'user'],
            ],
            'i18n' => [
                'translations' => [
                    'yii2mod.rbac' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@yii2mod/rbac/messages',
                    ],
                    // ...
                ],
            ]
    ],
    'modules' => [
        'rbac' => [
            'class' => 'yii2mod\rbac\Module',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            //
            'downloadAction' => 'gridview/export/download',
             'i18n' => [
                 'class' => 'yii\i18n\PhpMessageSource',
                 'basePath' => '@kvgrid/messages',
                 'forceTranslation' => true
             ],
        ],
            'api' => [
                'class' => 'app\modules\api\Api',
            ]
    ],
    'as access' => [
        'class' => yii2mod\rbac\filters\AccessControl::class,
        'allowActions' => [
            'rbac/*',
            'site/login',
            'site/login2',
            // 'invoices/list'
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ]
     ],
    'params' => $params,
];

if (class_exists('yii\debug\Module')) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
