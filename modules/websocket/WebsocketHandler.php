<?php

namespace app\modules\websocket;

use app\models\Chat;
use app\models\Message;
use app\models\User;
use Exception;
use Throwable;
use Workerman\Connection\ConnectionInterface;
use Workerman\Connection\TcpConnection;
use workerman\Worker;
use Yii;
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
            call_user_func_array([$this, $method], [$decodedData]);
        } catch (Exception $e) {
            $connection->send(Yii::t('app', $e->getMessage()));
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
    private function chooseMethod(int $type): string
    {
        if ($type > sizeof(self::CHAT_METHODS) || $type < 0) {
            throw new Exception("Invalid type of action");
        }
        return self::CHAT_METHODS[$type];
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
     * @param array $data
     * @throws Exception
     */
    private function addMessage(array $data)
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
        $messageInstance = new Message([
            'message' => $message,
            'user_id' => $ownerOfMessage->id,
            'chat_id' => $chatId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        if (!$messageInstance->save()) {
            throw new Exception($messageInstance->getErrors());
        }
        $users = Chat::findOne($chatId)->getUsers()
            ->select('id')->asArray()
            ->all();

        $data = array_merge(['username' => $ownerOfMessage->username], $messageInstance->toArray());
        $response = ['type' => self::TYPE_ADD_MESSAGE, 'data' => $data];

        $this->sendResponse($response, $users, $connectionId,
            self::TYPE_ADD_MESSAGE, self::TYPE_SENDED_MESSAGE);
    }

    /**
     * @param array $dataForSend
     * @param array $users
     * @param int $connectionId
     * @param int $typeForSender
     * @param int $typeForOthers
     */
    private function sendResponse(array $dataForSend, array $users, int $connectionId, int $typeForSender, int $typeForOthers): void
    {
        foreach ($users as $user) {
            if (key_exists($user['id'], $this->worker->connections)) {
                if ($connectionId == $user['id']) {
                    $dataForSend['type'] = $typeForSender;
                    $dataForSend = Json::encode($dataForSend);
                    $this->worker->connections[$user['id']]->send($dataForSend);
                } else {
                    $dataForSend['type'] = $typeForOthers;
                    $dataForSend = Json::encode($dataForSend);
                    $this->worker->connections[$user['id']]->send($dataForSend);
                }
            }
        }
    }

    private function updateMessage(array $data): void
    {
        try {
            $messageId = $data['message_id'];
            $connectionId = $data['connection_id'];
            $message = $data['message'];
            $messageInstance = Message::findOne($messageId);
            if (!$messageInstance) {
                throw new Exception('Not found message');
            }
            if ($messageInstance->user_id != $connectionId) {
                throw new Exception('Permission denied: you cannot delete message from another user');
            }
            $users = $messageInstance->chat->getUsers()->select('id')->all();
            if (!$users) {
                throw new Exception("Not users in chat");
            }
            $messageInstance->message = $message;
            $messageInstance->updated_at = date('Y-m-d H:i:s');
            if (!$messageInstance->save()) {
                throw new Exception($messageInstance->getErrors());
            }
            $data = array_merge($messageInstance->toArray(), ['status' => true]);
            $this->sendResponse($data, $users, $connectionId,
                self::TYPE_UPDATE_MESSAGE, self::TYPE_UPDATE_MESSAGE);
        }catch (Exception $e){
            $this->worker->connections[$connectionId]->send(Json::encode(
                [
                    'type'=>self::TYPE_UPDATE_MESSAGE,
                    'status'=>false,
                    'message_id'=>$messageId,
                    'error'=>$e->getMessage()
                ]
            ));
            throw $e;
        }
    }

    /**
     * @param array $data
     * @throws Throwable
     */
    private function deleteMessage(array $data): void
    {
        try {
            $messageId = $data['message_id'];
            $connectionId = $data['connection_id'];
            $message = Message::findOne($messageId);
            if (!$message or !$message->delete()) {
                throw new Exception("Cannot delete message");
            }
            if ($message->user_id != $connectionId) {
                throw new Exception("Permission denied for this user");
            }
            $users = $message->chat->getUsers()->select('id')->all();
            if (!$users) {
                throw new Exception("Not users in chat");
            }
            $this->sendResponse(["status" => true, 'message_id' => $messageId], $users, $connectionId,
                self::TYPE_DELETE_MESSAGE, self::TYPE_DELETE_MESSAGE);
        } catch (Exception $e) {
            $this->worker->connections[$connectionId]->send(Json::encode(
                [
                    'error' => $e->getMessage(),
                    'type' => self::TYPE_DELETE_MESSAGE,
                    'status' => false,
                    'message_id' => $messageId
                ]));
            throw $e;
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
}