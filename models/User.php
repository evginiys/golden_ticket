<?php

namespace app\models;

use app\models\behaviors\MongoLogger;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use DateTime;

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
 * @property string     $date_token_expired [datetime]
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            MongoLogger::class,
        ];
    }

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
     * @inheritDoc
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::find()
        ->andWhere(['token' => $token])
        ->andWhere(['<', 'date_token_expired', strtotime('-' . env('TOKEN_LIFE_TIME') . ' seconds')]);
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
            $this->token = Yii::$app->security->generateRandomString();
        }

        return $this->token;
    }

    /**
     * Updates an expiration date of API token based on value from .env file
     * @return void
     * @throws \Exception Emits Exception in case of an error.
     */
    public function updateTokenExpirationDate()
    {
        $this->date_token_expired = (new DateTime('+' . env('TOKEN_LIFE_TIME', 86400) . ' seconds'))->format('Y-m-d H:i:s');
    }
}
