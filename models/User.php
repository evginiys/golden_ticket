<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property string $token
 * @property string|null $reset_password_token
 * @property string|null $date_reset_password
 *
 * @property string $authKey
 * @property GameUser[] $gameUsers
 * @property string $date_token_expired [datetime]
 * @property Chat[] $ownChats
 * @property Chat[] $inChats
 * @property ChatUser[] $chatUsers
 * @property Message[] $messages
 * @property MessageStatus[] $messageStatus
 * @property Chat[] $gameChats
 */

class User extends ActiveRecord implements IdentityInterface
{
    public const ROLE_PLAYER = 'player';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_BANNED = 'banned';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password', 'token'], 'required'],
            [['date_reset_password', 'date_token_expired'], 'safe'],
            [['username'], 'string', 'max' => 45],
            [['email', 'password', 'token', 'reset_password_token'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 15],
            [['phone'], 'default'],
            [['email'], 'email'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'password' => Yii::t('app', 'Password'),
            'token' => Yii::t('app', 'Token'),
            'reset_password_token' => Yii::t('app', 'Reset Password Token'),
            'date_reset_password' => Yii::t('app', 'Date Reset Password'),
        ];
    }

    /**
     * @param null|int $key
     * @return array|string
     */
    public static function getRoleDescription($key = null)
    {
        $data = [
            self::ROLE_PLAYER => Yii::t('app', 'Player'),
            self::ROLE_ADMIN => Yii::t('app', 'Administrator'),
            self::ROLE_BANNED => Yii::t('app', 'Banned')
        ];

        return $data[$key] ?? $data;
    }

    /**
     * Gets query for [[GameUsers]].
     *
     * @return ActiveQuery
     */
    public function getGameUsers()
    {
        return $this->hasMany(GameUser::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Game]].
     * @return ActiveQuery
     */
    public function getInGame()
    {
        return $this->hasMany(Game::class, ['id' => 'game_id'])
            ->via('gameUsers');
    }

    /**
     * Gets query for [[Game]].
     * @return ActiveQuery
     */
    public function getGameChats()
    {
        return $this->hasMany(Chat::class, ['game_id' => 'id'])
            ->via('inGame');
    }

    /**
     * Gets query for [[ChatUsers]].
     * @return ActiveQuery
     */
    public function getChatUsers()
    {
        return $this->hasMany(ChatUser::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Chat]].
     * @return ActiveQuery
     */
    public function getOwnChats()
    {
        return $this->hasMany(Chat::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Message]].
     * @return ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::class, ['user_id' => 'id']);
    }

    /**
     * * Gets query for [[Socials]].
     *
     * @return ActiveQuery
     */
    public function getSocials()
    {
        return $this->hasMany(Social::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[MessageStatus]].
     * @return ActiveQuery
     */
    public function getMessageStatuses()
    {
        return $this->hasMany(MessageStatus::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Chat]].
     * @return ActiveQuery
     */
    public function getInChats()
    {
        return $this->hasMany(Chat::class, ['id' => 'chat_id'])
            ->via('chatUsers');
    }

    /**
     * @inheritDoc
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return array|ActiveRecord|IdentityInterface|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::find()
        ->andWhere(['token' => $token])
        ->andWhere(['>', 'date_token_expired', date('Y-m-d H:i:s')])
        ->one();
    }

    /**
     * Finds a user by the given username.
     *
     * @param $username
     * @return User|null
     */
    public static function findByUsername(string $username)
    {
        return self::findOne(['username' => $username]);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->token === $authKey;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Generates authentication token for API
     *
     * @param bool $force
     * @return string
     * @throws Exception
     */
    public function generateApiToken($force = false)
    {
        if (empty($this->token) || $force) {
            do {
                $this->token = Yii::$app->security->generateRandomString();
                $isExist = self::find()->andWhere(['token' => $this->token])->one();
            } while ($isExist);
        }

        return $this->token;
    }

    /**
     * Updates an expiration date of API token based on value from .env file
     *
     * @return bool whether the updating succeeded
     * @throws \Exception in case of an error.
     */
    public function updateTokenExpirationDate()
    {
        $this->date_token_expired = date('Y-m-d H:i:s', strtotime('+' . env('TOKEN_LIFE_TIME', 86400) . ' seconds'));
        return $this->save(false);
    }

    /**
     * @param float $amount
     * @param int $currency
     * @return bool
     */
    public function canPay(float $amount, int $currency = Payment::CURRENCY_COIN): bool
    {
        return $amount <= $this->getBalance($currency);
    }

    /**
     * @param $currency
     * @return float
     */
    public function getBalance($currency)
    {
        $balance = ArrayHelper::index(Payment::getUserBalance($this->id), 'currency')[$currency] ?? 0;
        if (empty($balance)) {
            return $balance;
        }
        return (float)$balance['balance'];
    }

}
