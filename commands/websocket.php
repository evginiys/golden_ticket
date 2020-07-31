<?php

$yiiFolder = dirname(__FILE__, 2);
require($yiiFolder . '/vendor/autoload.php');
require($yiiFolder . '/vendor/yiisoft/yii2/Yii.php');
require_once Yii::getAlias('vendor') . '/workerman/workerman/Autoloader.php';

$config = require($yiiFolder . '/config/console.php');
new yii\console\Application($config);

use app\modules\websocket\WebsocketHandler;
use Workerman\Connection\TcpConnection;
use workerman\Worker;

$websocket = new WebsocketHandler(env('SOCKET_HOST'), env('SOCKET_PORT'));
$websocket->setParams([
    'count' => 4,
]);
$wsWorker = $websocket->getWorker();

/**
 * @param TcpConnection $connection
 */
$wsWorker->onConnect = static function($connection) use ($websocket)
{
    $websocket->onConnect($connection);
};

/**
 * @param Workerman\Connection\TcpConnection $connection
 * @param string $data
 */
$wsWorker->onMessage = static function($connection, $data) use($websocket)
{
    $websocket->onMessage($connection, $data);
};

/**
 * @param Workerman\Connection\TcpConnection $connection
 */
$wsWorker->onClose = static function($connection) use ($websocket)
{
    $websocket->onClose($connection);
};

$wsWorker->onWorkerStart = static function ($worker) use ($websocket) {
    $websocket->workerStart($worker);
};

Worker::runAll();