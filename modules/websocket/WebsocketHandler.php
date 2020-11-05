<?php

namespace app\modules\websocket;

use app\models\Chat;
use app\models\ChatUser;
use app\models\Message;
use app\models\User;
use Exception;
use Throwable;
use Workerman\Connection\ConnectionInterface;
use Workerman\Connection\TcpConnection;
use workerman\Worker;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class WebsocketHandler
 * @package app\modules\websocket
 */
class WebsocketHandler
{
    private const TYPE_GET_TOKEN = 0;
    private const TYPE_ADD_MESSAGE = 1;
    private const TYPE_DELETE_MESSAGE = 2;
    private const TYPE_UPDATE_MESSAGE = 3;
    private const TYPE_CREATE_CHAT = 4;
    private const TYPE_LEAVE_CHAT = 5;
    private const TYPE_GET_MESSAGES = 6;
    private const TYPE_SENDED_MESSAGE = 7;

    private const QUANTITY_OF_MESSAGES = 15;

    private const CHAT_METHODS = [
        'setConnectionId',
        'addMessage',
        'deleteMessage',
        'updateMessage',
        'createChat',
        'leaveChat',
        'getMessages',
    ];

    /** @var Worker */
    private $worker;

    /**
     * WebsocketHandler constructor.
     * @param string $host
     * @param string $port
     * @param array $context
     */
    public function __construct(string $host, string $port, array $context = [])
    {
        $this->worker = new Worker("websocket://{$host}:{$port}", $context);
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
     * @param $worker
     * @throws Exception
     */
    public function onWorkerStart($worker) {}

    /**
     * @param ConnectionInterface $connection
     */
    public function onConnect(ConnectionInterface $connection)
    {
        $connection->onWebSocketConnect = function ($connection) {
            $token = $_GET['token'] ?? null;
            if (!$token) {
                throw new Exception('Please send access token');
            }

            $user = User::findIdentityByAccessToken($token);
            if (!$user) {
                throw new Exception('Not found user');
            }

            $connection->user_id = $user->id; // dynamically added field
            //todo check existance of user_id and push new connection_id for user_id to mongodb

            echo '[Worker ' . $connection->worker->id . '] New connection accepted, CID=' . $connection->id . PHP_EOL;

            $connection->send('Connection is open');
        };
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onClose(ConnectionInterface $connection)
    {
        //todo remove connection_id for user_id from mongodb
        echo '[Worker ' . $connection->worker->id . '] Connection ' . $connection->id . ' closed' . PHP_EOL;
    }

    /**
     * Choose method by type of message
     *
     * @param TcpConnection $connection
     * @param string $data
     */
    public function onMessage(TcpConnection $connection, string $data)
    {
        echo '[Worker ' . $connection->worker->id . '] User ' . $connection->user_id . ' sent message to connection ' . $connection->id . PHP_EOL;
//        try {
//            $decodedData['connection_id'] = $connection->id;
//            $decodedData = array_merge($decodedData, Json::decode($data));
//            if ($decodedData['type'] == self::TYPE_GET_TOKEN) {
//                $this->setConnectionId($decodedData, $connection);//connection->id=user->id
//                return;
//            }
//            if (!$decodedData['type'] || !is_numeric($decodedData['type']) || !is_int(+$decodedData['type'])) {
//                throw new Exception("Invalid argument 'type'");
//            }
//            $method = $this->chooseMethod($decodedData['type']);
//            if (!is_callable([$this, $method])) {
//                throw new Exception('Not found method');
//            }
//            call_user_func_array([$this, $method], [$decodedData]);
//        } catch (\yii\db\Exception $e) {
//            Yii::$app->db->close();
//            Yii::$app->db->open();
//        } catch (Exception $e) {
//            $connection->send(Yii::t('app', $e->getMessage()));
//            echo $e->getMessage() . "\n" . $e->getLine() . "\n" . $e->getFile() . "\n\n";
//        }
    }

    /**
     * Connection identificated by user id
     *
     * Set connection->id=user->id
     * Send data in format {"type":"0","token":"string"}
     *
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
        echo '[Worker ' . $connection->worker->id . '] ';
        echo 'Existing CIDs: ' .
             implode(' ', ArrayHelper::getColumn($this->worker->connections, 'id')) . ' - ' .
             count($this->worker->connections) . ' in total. ';
        if (key_exists($connection->id, $this->worker->connections)) {
            echo 'CID = ' . $connection->id .' already exists. Switching to user_id = ' . $idUser;
        }
        echo PHP_EOL;
        unset($this->worker->connections[$connection->id]);
        $connection->id = $idUser;
        $this->worker->connections[$idUser] = $connection;
        echo '[Worker ' . $connection->worker->id . '] ';
        echo 'Current CID = ' . $connection->id . ', worker CIDs: ' .
             implode(' ', ArrayHelper::getColumn($this->worker->connections, 'id')) . ' - ' .
             count($this->worker->connections) . ' in total' . PHP_EOL;
        //user can have one connection, which is identified by user id
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
     * Adds a new a message to database and returns status of adding
     *
     * Send data in format {"type":"1","chat_id":"int","message":"string"}
     *
     * @param array $data
     * @throws Exception
     */
    private function addMessage(array $data)
    {
        try {
            $connectionId = $data['connection_id'];
            $chatId = $data['chat_id'];
            $message = $data['message'];
            $ownerOfMessage = User::findOne($connectionId);//$connectionId equal user->id
            if (!$ownerOfMessage) {
                throw new Exception('Not found user, repeat token send');
            }
            $chat = $ownerOfMessage->getInChats()->where(['id' => $chatId])->one();
            if (!$chat) {
                $chat = $ownerOfMessage->getGameChats()->where(['id' => $chatId])->one();
                if (!$chat) {
                    throw new Exception('Not found chat for this user');
                }
            }
            $messageInstance = new Message([
                'message' => $message,
                'user_id' => $ownerOfMessage->id,
                'chat_id' => $chatId,
            ]);
            if (!$messageInstance->save()) {
                throw new Exception($messageInstance->getErrors());
            }
            $users = Chat::findOne($chatId)->getUsers()
                ->select('id')->asArray()
                ->all();

            $data = array_merge(['username' => $ownerOfMessage->username], $messageInstance->toArray());
            $response = ['data' => $data];

            $this->sendResponse($response, $users, $connectionId,
                self::TYPE_ADD_MESSAGE, self::TYPE_SENDED_MESSAGE);
        } catch (Exception $e) {
            $this->worker->connections[$connectionId]->send(Json::encode([
                'type' => self::TYPE_SENDED_MESSAGE,
                'status' => false,
                'error' => $e->getMessage()
            ]));
            throw $e;
        }
    }

    /**
     * sendResponse
     *
     * Send $dataForSend to each user in array $users with $typeForOthers
     * and to user with id=connection->id with $typeForSender
     *
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
                    $dataForSend['status'] = true;
                    $dataEncoded = Json::encode($dataForSend);
                    $this->worker->connections[$user['id']]->send($dataEncoded);
                } else {
                    $dataForSend['type'] = $typeForOthers;
                    $dataEncoded = Json::encode($dataForSend);
                    $this->worker->connections[$user['id']]->send($dataEncoded);
                }
            }
        }
    }

    /**
     * Update data of message
     *
     * Send data in format {"type":"3","message":"string","message_id":"int"}
     *
     * @param array $data
     * @throws Exception
     */
    private function updateMessage(array $data): void
    {
        $connectionId = $data['connection_id'];
        try {
            $messageId = $data['message_id'];
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
            if (!$messageInstance->save()) {
                throw new Exception($messageInstance->getErrors());
            }
            $data = $messageInstance->toArray();
            $this->sendResponse($data, $users, $connectionId,
                self::TYPE_UPDATE_MESSAGE, self::TYPE_UPDATE_MESSAGE);
        } catch (Exception $e) {
            $this->worker->connections[$connectionId]->send(Json::encode(
                [
                    'type' => self::TYPE_UPDATE_MESSAGE,
                    'status' => false,
                    'message_id' => $messageId,
                    'error' => $e->getMessage()
                ]
            ));
            throw $e;
        }
    }

    /**
     * Delete message
     *
     * Send data in format {"type":"2","message_id":"int"}
     *
     * @param array $data
     * @throws Throwable
     */
    private function deleteMessage(array $data): void
    {
        try {
            $messageId = $data['message_id'];
            $connectionId = $data['connection_id'];
            $message = Message::findOne($messageId);
            if (!$message) {
                throw new Exception("Not found message");
            }
            if ($message->user_id != $connectionId) {
                throw new Exception("Permission denied for this user");
            }
            if (!$message->delete()) {
                throw new Exception("Cannot delete message");
            }
            $users = $message->chat->getUsers()->select('id')->all();
            if (!$users) {
                throw new Exception("Not users in chat");
            }
            $this->sendResponse(['message_id' => $messageId, 'chat_id' => $message->chat_id], $users, $connectionId,
                self::TYPE_DELETE_MESSAGE, self::TYPE_DELETE_MESSAGE);
        } catch (Exception $e) {
            $this->worker->connections[$connectionId]->send(Json::encode(
                [
                    'error' => $e->getMessage(),
                    'type' => self::TYPE_DELETE_MESSAGE,
                    'status' => false,
                    'message_id' => $messageId,
                ]));
            throw $e;
        }
    }

    /**
     * Create Chat
     *
     * Send data in format {"type":"4","name":"string","user":{"id":"int"}}
     *
     * @param array $data
     */
    private function createChat(array $data): void
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $connectionId = $data['connection_id'];
            $participant = $data['user'];
            $chatName = $data['name'];
            $participantUser = User::findOne($participant);
            if (!$participantUser) {
                throw new Exception("Not found participant of chat");
            }
            $chatsSecondUser = $participantUser
                ->getInChats()
                ->where(['type' => Chat::TYPE_PRIVATE])
                ->select('id')
                ->asArray()
                ->all();
            $chatsFirstUser = User::findOne($connectionId)
                ->getInChats()
                ->where(['type' => Chat::TYPE_PRIVATE])
                ->select('id')
                ->andWhere(['id' => $chatsSecondUser])
                ->one();
            if ($chatsFirstUser) {
                throw new Exception("Chat with this user already exist");
            }
            $chat = new Chat([
                'user_id' => $connectionId,//connection indentificated by user_id
                'name' => $chatName,
                'type' => Chat::TYPE_PRIVATE
            ]);
            if (!$chat->save()) {
                throw new Exception($chat->getErrors());
            }
            $chat->addUserToChat($connectionId);

            if (key_exists('id', $participant) &&
                is_numeric($participant['id']) &&
                is_int(+$participant['id'])) {
                $chat->addUserToChat($participant['id']);
            } else {
                throw new Exception("Not found user for chat");
            }

            $participant = $chat->getUsers()->select('id')->asArray()->all();
            $dataForResponse = $chat->toArray();
            $this->sendResponse($dataForResponse, $participant, $connectionId,
                self::TYPE_CREATE_CHAT, self::TYPE_CREATE_CHAT);
            $transaction->commit();
        } catch (Exception $e) {
            $this->worker->connections[$connectionId]->send(Json::encode([
                'error' => $e->getMessage(),
                'type' => self::TYPE_CREATE_CHAT,
                'status' => false,
            ]));
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Leave chat
     *
     * Send data in format {"type":"5","chat_id":"int"}
     *
     * @param array $data
     * @throws Throwable
     */
    private function leaveChat(array $data): void
    {
        try {
            $connectionId = $data['connection_id'];
            if (!key_exists('chat_id', $data)) {
                throw new Exception("Argument chat_id not found in input data");
            }
            $chatId = $data['chat_id'];
            $chat = Chat::findOne($chatId);
            if (!$chat) {
                throw new Exception("Not found chat");
            }
            if ($chat->game) {
                throw new Exception("Cannot leave game chat");
            }
            $chatUser = ChatUser::find()->where(['chat_id' => $chatId, 'user_id' => $connectionId])->one();
            if (!$chatUser) {
                throw new Exception("Not found user in chat");
            }
            $users = $chat->getUsers()->select('id')->asArray()->all();
            if (!$users) {
                $chat->delete();
                throw new Exception("Chat without users");
            }
            if (!$chatUser->delete()) {
                throw new Exception("Cannot leave chat");
            }
            $data = array('chat_id' => $chatId, 'user_id' => $connectionId);
            $this->sendResponse($data, $users, $connectionId,
                self::TYPE_LEAVE_CHAT, self::TYPE_LEAVE_CHAT);
        } catch (Exception $e) {
            $this->worker->connections[$connectionId]->send(Json::encode([
                'status' => false,
                'error' => $e->getMessage(),
                'type' => self::TYPE_LEAVE_CHAT,
                'chat_id' => $chatId
            ]));
            throw $e;
        }
    }

    /**
     * Get messages
     *
     * Send data in format {"type":"6","chat_id":"int","message_id":"int","quantity":int}
     *
     * @param array $data
     * @throws Exception
     */
    private function getMessages(array $data): void
    {
        try {
            if (!key_exists('chat_id', $data)) {
                throw new Exception('Incorrect incoming data: missing chat_id argument');
            }
            $chatId = $data['chat_id'];
            $connectionId = $data['connection_id'];
            if (key_exists('message_id', $data)) {
                $messageFrom = $data['message_id'];
                if (!is_numeric($messageFrom) && !is_int(+$messageFrom)) {
                    throw new Exception('Incorrect incoming data: message_id argument is incorrect');
                }
            }
            if (key_exists('quantity', $data)) {
                $quantity = $data['quantity'];
                if (!is_numeric($quantity) && !is_int(+$quantity)) {
                    throw new Exception('Incorrect incoming data: quantity argument is incorrect');
                }
            }
            $chat = Chat::findOne($chatId);
            if (!$chat) {
                throw new Exception('Not found chat');
            }
            $users = $chat->getUsers()->select(['id'])->all();
            if (!$users) {
                throw new Exception('Not found participants of this chat');
            }
            if (isset($messageFrom)) {
                $messages = $chat->getMessages()->where(['<', 'messages.id', $messageFrom])
                    ->innerJoinWith('user')
                    ->orderBy('messages.id desc')->limit($quantity ?? self::QUANTITY_OF_MESSAGES)->all();
            } else {
                $messages = $chat->getMessages()->orderBy('messages.id desc')
                    ->innerJoinWith('user')
                    ->limit($quantity ?? self::QUANTITY_OF_MESSAGES)->all();
            }
            $data = [];
            foreach ($messages as $message) {
                $data[] = array_merge($message->toArray(),
                    $message->user->getAttributes([
                        'username',
                        'email'
                    ]));
            }
            $this->worker->connections[$connectionId]->send(Json::encode([
                'status' => true,
                'type' => self::TYPE_GET_MESSAGES,
                'data' => $data
            ]));
        } catch (Exception$e) {
            $this->worker->connections[$connectionId]->send(Json::encode([
                'status' => false,
                'type' => self::TYPE_GET_MESSAGES,
                'error' => $e->getMessage()
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