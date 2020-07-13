<?php

use app\modules\api\v1\ApiModule;
use yii\rest\UrlRule;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$authManager = require __DIR__ . '/auth_manager.php';
$mongodb = require __DIR__ . '/mongodb.php';

$config = [
    'id' => 'basic',
    'name' => 'Golden Ticket',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => ApiModule::class,
        ],
    ],
    'components' => [
        'authManager' => $authManager,
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'WugW1ka6Ar_vkMsuJ-2mUK2DJ-Vx-RUY',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
        'errorHandler' => [
            'errorAction' => 'v1/common/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
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
        'db' => $db,
        'mongodb' => $mongodb,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
                [
                    'class' => UrlRule::class,
                    'prefix' => 'api',
                    'controller' => [
                        'v1/user',
                        'v1/ticket',
                        'v1/common',
                        'v1/game',
                    ],
                    'extraPatterns' => [
                        'POST sign-in' => 'sign-in',
                        'POST sign-up' => 'sign-up',
                        'POST logout' => 'logout',
                        'POST forgot-password' => 'forgot-password',
                        'GET reset-password' => 'reset-password-get',
                        'POST reset-password' => 'reset-password-post',
                        'GET packs' => 'packs',
                        'GET tickets' => 'tickets',
                        'POST buy' => 'buy',
                        'GET error' => 'error',
                        'GET games' => 'games',
                        'POST bet' => 'bet',
                        'POST check' => 'check',
                    ],
                    'pluralize' => false,
                ]
            ],
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
