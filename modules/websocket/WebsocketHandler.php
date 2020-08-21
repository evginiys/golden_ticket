<?php

namespace app\modules\websocket;

use app\models\Chat;
use app\models\Message;
use app\models\User;
use Exception;
use Workerman\Connection\ConnectionInterface;
use Workerman\Connection\TcpConnection;
use workerman\Worker;
use yii\helpers\Json;

/**
 * Class WebsocketHandler
 * @package app\modules\websocket
 */
class WebsocketHandler
{
    //
    public const TYPE_GET_TOKEN = 0;
    public const TYPE_ADD_MESSAGE = 1;
    public const TYPE_DELETE_MESSAGE = 2;
    public const TYPE_UPDATE_MESSAGE = 3;
    public const TYPE_CREATE_CHAT = 4;
    public const TYPE_DELETE_CHAT = 5;
    public const TYPE_GET_MESSAGE = 6;
    //
    public const CHAT_METHODS = [
        'setConnectionId',
        'addMessage',
        'deleteMessage',
        'updateMessage',
        'createChat',
        'deleteChat',
    ];

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
     * @param TcpConnection $connection
     * @param string $data
     */
    public function onMessage(TcpConnection $connection, string $data)
    {
        try {
            $decodedData['connection_id'] = $connection->id;
            $decodedData = array_merge($decodedData, Json::decode($data));
            if ($decodedData['type'] == self::TYPE_GET_TOKEN) {
                $this->setConnectionId($decodedData, $connection);//connection->id=user->id
                return;
            }
            if (!$decodedData['type'] || !is_numeric($decodedData['type']) || !is_int(+$decodedData['type'])) {
                throw new Exception("Invalid argument 'type'");
            }
            $method = $this->chooseMethod($decodedData['type']);
            if (!is_callable([$this, $method])) {
                throw new Exception('Not found method');
            }
            $dataForSend = call_user_func_array([$this, $method], $decodedData);

            $users = $dataForSend['users'];
            unset($dataForSend['users']);
            $dataForSend = Json::encode($dataForSend);
            foreach ($users as $userId) {
                if ($this->worker->connections[$userId]) {
                    $this->worker->connections[$userId]->send($dataForSend);
                }
            }
//            if (($message['broadcast'] ?? 0) == true) {
//                $this->broadCast($message['message']);
//            } else {
//                $connection->send($message['message']);
//            }
        } catch (Exception $e) {
            $connection->send($e->getMessage());
        }
    }

    /**
     * @param int $type
     * @return string
     * @throws Exception
     */
    public function chooseMethod(int $type): string
    {
        if ($type > sizeof(self::CHAT_METHODS) || $type < 0) {
            throw new Exception("Invalid type of action");
        }
        return self::CHAT_METHODS[$type];
    }

    /**
     * @param array $data
     * @param TcpConnection $connection
     * @throws Exception
     */
    public function setConnectionId(array $data, TcpConnection $connection): void
    {
        if (!$data['token']) {
            throw new Exception('Not found token');
        }
        $user = User::findIdentityByAccessToken($data['token']);
        if (!$user) {
            throw new Exception('Invalid token, not found user');
        }
        $idUser = $user->getId();
        if ($this->worker->connections[$idUser]) {
            $this->worker->connections[$idUser]->close();
        }
        echo $connection->id . "\n";
        $connection->id = $idUser;
        echo $connection->id;
        //user can have one connection, which identificate connection by user id
    }


    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function addMessage(array $data): array
    {
        $connectionId = $data['connection_id'];
        $chatId = $data['chat_id'];
        $message = $data['message'];
        $user = User::findOne($connectionId);//$connectionId equal user->id
        if (!$user) {
            throw new Exception('Not found user, repeat token send');
        }
        $chat = $user->getInChats()->where(['id' => $chatId])->one();
        if (!$chat) {
            throw new Exception('Not found chat for this user');
        }
        $messageInstanse = new Message([
            'message' => $message,
            'user_id' => $user->id,
            'chat_id' => $chatId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        if (!$messageInstanse->save()) {
            throw new Exception($messageInstanse->getErrors());
        }
        $users = Chat::findOne($chatId)->getUsers()
            ->where(['not', ['id' => $user->id]])
            ->select('id')->asArray()
            ->all();
        $data = array_merge($user->toArray(), $messageInstanse->toArray());
        $response = ['type' => self::TYPE_ADD_MESSAGE, 'users' => $users, 'data' => $data];
        return $response;
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
        $connection->send('ok');
    }

    /**
     * @param $worker
     * @throws Exception
     */
    public function workerStart($worker)
    {
        //@todo do something
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
}