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
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => ApiModule::class,
        ],
        'pro-admin' => [
            'class' => 'app\modules\admin\AdminModule',
        ],
    ],
    'components' => [
        'authManager' => $authManager,
        'request' => [
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
            'traceLevel' => 0,
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
                        'v1/dashboard',
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
                        'GET choosen-combination' => 'choosen-combination',
                        'POST bet' => 'bet',
                        'POST check' => 'check',
                        'GET online-count' => 'online-count',
                        'GET user-inf-by-token' => 'user-inf-by-token',
                        'GET get-balance' => 'get-balance',
                        'POST change-name' => 'change-name',
                        'GET get-rate' => 'get-rate',
                        'POST exchange' => 'exchange',
                        'POST refill' => 'refill'
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
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
