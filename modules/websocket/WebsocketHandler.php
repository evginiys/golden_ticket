<?php

namespace app\modules\websocket;

use app\modules\websocket\handlers\BaseHandler;
use Workerman\Connection\ConnectionInterface;
use \workerman\Worker;
use yii\helpers\Json;

/**
 * Class WebsocketHandler
 * @package app\modules\websocket
 */
class WebsocketHandler
{
    /** @var Worker */
    private $worker;

    /**
     * WebsocketHandler constructor.
     * @param string $host
     * @param string $port
     */
    public function __construct(string $host, string $port)
    {
        $this->worker = new Worker("websocket://{$host}:{$port}");
    }

    /**
     * @return Worker
     */
    public function getWorker(): Worker
    {
        return $this->worker;
    }

    /**
     * @param $params
     * @return static
     */
    public function setParams($params): self
    {
        foreach ($params as $key => $value) {
            $this->worker->{$key} = $value;
        }

        return $this;
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $data
     */
    public function onMessage(ConnectionInterface $connection, string $data)
    {
        $message = Json::decode($data);
        $handler = BaseHandler::getHandler($message['handle'] ?? 'base');
        $handler->setData($message['data'] ?? [])->handle();

        $dataToSend = Json::encode($message['data'] ?? []);
        if (($message['broadcast'] ?? 0) == true) {
            $this->broadCast($dataToSend);
        } else {
            $connection->send($dataToSend);
        }
    }

    /**
     * @param string $message
     */
    private function broadCast(string $message): void
    {
        $cnt = count($this->worker->connections);
        foreach ($this->worker->connections as $connection) {
            $connection->send($message . ' Broadcast: ' . $cnt);
        }
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onClose(ConnectionInterface $connection)
    {
        //@todo do something
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onConnect(ConnectionInterface $connection)
    {

    }

    /**
     * @param $worker
     * @throws \Exception
     */
    public function workerStart($worker)
    {
        //@todo do something
    }
}