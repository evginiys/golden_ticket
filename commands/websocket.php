<?php

$yiiFolder = dirname(__FILE__, 2);
require($yiiFolder . '/vendor/autoload.php');
require($yiiFolder . '/vendor/yiisoft/yii2/Yii.php');
require_once $yiiFolder . '/vendor/workerman/workerman/Autoloader.php';

$config = require($yiiFolder . '/config/console.php');
new yii\console\Application($config);

use app\modules\websocket\WebsocketHandler;
use Workerman\Connection\TcpConnection;
use workerman\Worker;

const DEFAULT_WORKERS_NUMBER = 4;

$websocketParams = [
    'count' => env('WEBSOCKET_WORKERS_NUMBER', DEFAULT_WORKERS_NUMBER)
];

$context = [];
if (env('WEBSOCKET_USE_SSL')) {
    $context = [
        'ssl' => [
            'local_cert' => env('WEBSOCKET_CERT'),
            'local_pk' => env('WEBSOCKET_PK'),
            'verify_peer' => false,
        ]
    ];
    $websocketParams['transport'] = 'ssl';
}

$websocket = new WebsocketHandler(env('WEBSOCKET_HOST'), env('WEBSOCKET_PORT'), $context);
$websocket->setParams($websocketParams);

$wsWorker = $websocket->getWorker();

/**
 * @param TcpConnection $connection
 */
$wsWorker->onConnect = static function ($connection) use ($websocket) {
    $websocket->onConnect($connection);
};

/**
 * @param Workerman\Connection\TcpConnection $connection
 * @param string $data
 */
$wsWorker->onMessage = static function ($connection, $data) use ($websocket) {
    $websocket->onMessage($connection, $data);
};

/**
 * @param Workerman\Connection\TcpConnection $connection
 */
$wsWorker->onClose = static function ($connection) use ($websocket) {
    $websocket->onClose($connection);
};

$wsWorker->onWorkerStart = static function ($worker) use ($websocket) {
    $websocket->onWorkerStart($worker);
};

Worker::runAll();
