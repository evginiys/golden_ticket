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
     * Gets query for [[GameUsers]].
     *
     * @return ActiveQuery
     */
    public function getGameUsers()
    {
        return $this->hasMany(GameUser::class, ['user_id' => 'id']);
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

    /**
     *
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    public function getTickets()
    {
        try {
            $minus = Payment::find()
                ->where(['type' => Payment::TYPE_CHARGE, 'to_user_id' => $this->id])
                ->andWhere(['not', ['ticket_id' => null]])
                ->innerJoinWith('ticket', 'ticket_id=ticket.id')
                ->innerJoin('ticket_pack', 'ticket_pack.id=ticket.ticket_pack_id')
                ->select(['ticket_pack.name', 'COUNT(ticket.id) AS quantity'])
                ->groupBy('ticket_pack.name')
                ->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql;
            //->all();

            $commandMinus = Yii::$app->db->createCommand(
                "SELECT `ticket_pack`.`name`, COUNT(ticket.id) AS `quantity` FROM `payment` 
                INNER JOIN `ticket` ON `payment`.`ticket_id` = `ticket`.`id` 
                INNER JOIN `ticket_pack` ON ticket_pack.id=ticket.ticket_pack_id 
                WHERE ((`type`=:type) AND (`to_user_id`=:user_id)) 
                AND (NOT (`ticket_id` IS NULL)) 
                GROUP BY `ticket_pack`.`name`");
            $minus = $commandMinus->bindValue(':type', Payment::TYPE_CHARGE)
                ->bindValue(':user_id', $this->id)
                ->queryAll();

            $commandPlus = Yii::$app->db->createCommand(
                "SELECT `ticket_pack`.`name`, COUNT(ticket.id) AS `quantity` FROM `payment` 
                INNER JOIN `ticket` ON `payment`.`ticket_id` = `ticket`.`id` 
                INNER JOIN `ticket_pack` ON ticket_pack.id=ticket.ticket_pack_id 
                WHERE ((`type`=:type) AND (`from_user_id`=:user_id)) 
                AND (NOT (`ticket_id` IS NULL)) 
                GROUP BY `ticket_pack`.`name`");
            $plus = $commandPlus->bindValue(':type', Payment::TYPE_BUY)
                ->bindValue(':user_id', $this->id)
                ->queryAll();

        } catch (Exception $e) {
            throw new Exception(Yii::t('app', $e->getMessage()));
        }
        // $ticketCount = $plus - $minus;
//        if ($ticketCount < 0) {
//            throw new Exception(Yii::t('app', 'Error, negative quantity of tickets'));
//        }

        return $plus;
    }

}
