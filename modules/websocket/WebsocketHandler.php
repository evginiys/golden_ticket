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
    private const TYPE_GET_TOKEN = 0;
    private const TYPE_ADD_MESSAGE = 1;
    private const TYPE_DELETE_MESSAGE = 2;
    private const TYPE_UPDATE_MESSAGE = 3;
    private const TYPE_CREATE_CHAT = 4;
    private const TYPE_DELETE_CHAT = 5;
    private const TYPE_SENDED_MESSAGE = 6;
    //

    private const CHAT_METHODS = [
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
            $dataForSend = call_user_func_array([$this, $method], [$decodedData]);
            $users = $dataForSend['users'];
            unset($dataForSend['users']);

            foreach ($users as $user) {
                if (key_exists($user['id'], $this->worker->connections)) {
                    if ($connection->id == $user['id']) {
                        $dataForSend['type'] = self::TYPE_SENDED_MESSAGE;
                        $dataForSend = Json::encode($dataForSend);
                        $this->worker->connections[$user['id']]->send($dataForSend);
                    } else {
                        $dataForSend['type'] = self::TYPE_ADD_MESSAGE;
                        $dataForSend = Json::encode($dataForSend);
                        $this->worker->connections[$user['id']]->send($dataForSend);
                    }
                }
            }

        } catch (Exception $e) {
            $connection->send($e->getMessage() . $e->getLine() . $e->getFile());
            echo $e->getMessage() . $e->getLine() . $e->getFile();
        }
    }

    /**
     * @param array $data
     * @param TcpConnection $connection
     * @throws Exception
     */
    public function setConnectionId(array $data, TcpConnection $connection): void
    {
        if (!key_exists('token', $data)) {
            throw new Exception('Not found token');
        }
        $user = User::findIdentityByAccessToken($data['token']);
        if (!$user) {
            throw new Exception('Invalid token, not found user');
        }
        $idUser = $user->getId();
        if (key_exists($idUser, $this->worker->connections)) {
            echo "exist connection";
        }
        $connection->id = $idUser;
        echo $connection->id;
        //user can have one connection, which identificate connection by user id
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
     * @return array
     * @throws Exception
     */
    public function addMessage(array $data): array
    {
        $connectionId = $data['connection_id'];
        $chatId = $data['chat_id'];
        $message = $data['message'];
        $ownerOfMessage = User::findOne($connectionId);//$connectionId equal user->id
        if (!$ownerOfMessage) {
            throw new Exception('Not found user, repeat token send');
        }
        $chat = $ownerOfMessage->getInChats()->where(['id' => $chatId])->one();
        if (!$chat) {
            throw new Exception('Not found chat for this user');
        }
        $messageInstanse = new Message([
            'message' => $message,
            'user_id' => $ownerOfMessage->id,
            'chat_id' => $chatId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        if (!$messageInstanse->save()) {
            throw new Exception($messageInstanse->getErrors());
        }
        $users = Chat::findOne($chatId)->getUsers()
            ->select('id')->asArray()
            ->all();

        $data = array_merge(['username' => $ownerOfMessage->username], $messageInstanse->toArray());
        $response = ['type' => self::TYPE_ADD_MESSAGE, 'users' => $users, 'data' => $data];
        return $response;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onClose(ConnectionInterface $connection)
    {
        $connection->send('Connection closed');
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onConnect(ConnectionInterface $connection)
    {
        $connection->send('Connection is ok');
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