<?php

namespace app\models;

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
 * @property ChatUser[] $chatUsers
 * @property Message[] $messages
 */
class Chat extends ActiveRecord
{

    public const TYPE_PRIVATE=0;
    public const TYPE_FOR_GAME=1;
    public const TYPE_COMMON=2;
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
            [['user_id', 'created_at', 'name'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
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
            'type'=>'Type',
            'created_at' => 'Created At',
            'name' => 'Name',
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
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->via('chatUsers');
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
}
