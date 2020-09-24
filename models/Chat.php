<?php

namespace app\models;

use Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property int $user_id
 * @property string $created_at
 * @property int $type
 * @property string $name
 *
 * @property User $user
 * @property User[] $users
 * @property Game $game
 * @property GameUser[] $gameUsers
 * @property ChatUser[] $chatUsers
 * @property Message[] $messages
 */
class Chat extends ActiveRecord
{

    public const TYPE_PRIVATE = 0;
    public const TYPE_FOR_GAME = 1;
    public const TYPE_COMMON = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['user_id', 'game_id'], 'integer'],
            [['name'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'game_id' => 'Game ID',
            'type' => 'Type',
            'created_at' => 'Created At',
            'name' => 'Name',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'value' => date('Y-m-d H:i:s'),
            ]
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Game]].
     *
     * @return ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Game::class, ['id' => 'game_id']);
    }

    /**
     * Gets query for [[GameUsers]].
     *
     * @return ActiveQuery
     */
    public function getGameUsers()
    {
        return $this->hasMany(GameUser::class, ['game_id' => 'id'])
            ->via('game');
    }

    /**
     * Gets query for [[ChatUsers]].
     *
     * @return ActiveQuery
     */
    public function getChatUsers()
    {
        return $this->hasMany(ChatUser::class, ['chat_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     * @return ActiveQuery
     */
    public function getUsers()
    {
        if ($this->type == self::TYPE_FOR_GAME) {
            return $this->getUsersInGame();
        } else {
            return $this->hasMany(User::class, ['id' => 'user_id'])
                ->via('chatUsers');
        }
    }

    /**
     * Gets query for [[GameUsers]].
     *
     * @return ActiveQuery
     */
    public function getUsersInGame()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->via('gameUsers');
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::class, ['chat_id' => 'id']);
    }

    /**
     * @param int $userId
     * @throws Exception
     */
    public function addUserToChat(int $userId): void
    {
        if (!User::findOne($userId)) {
            throw new Exception("Not found user");
        }
        $userChat = new ChatUser([
            'user_id' => $userId,
            'chat_id' => $this->id
        ]);
        if (!$userChat->save()) {
            throw new Exception($userChat->getErrors());
        }
    }
}
