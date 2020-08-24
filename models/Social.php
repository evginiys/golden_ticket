<?php

namespace app\models;

use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "social".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $social_id
 * @property string|null $social_client
 *
 * @property User $user
 */
class Social extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'social';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'social_id'], 'integer'],
            [['social_client'], 'string'],
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
            'social_id' => 'Social ID',
            'social_client' => 'Social Client',
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
     * @return string
     * @throws Exception
     */
    public function signIn()
    {
        $user = $this->user;
        $user->generateApiToken();
        $user->updateTokenExpirationDate();
        return $user->token;
    }

}
