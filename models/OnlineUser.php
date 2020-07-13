<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\mongodb\Query;

/**
 * Class OnlineUser
 * @package app\models
 * @property string _id
 * @property int user_id
 * @property bool is_online
 * @property int time_online
 * @property int last_timestamp
 */
class OnlineUser extends ActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'user_online';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'user_id', 'is_online', 'time_online', 'last_timestamp'];
    }

    /**
     * @param int $userId
     * @return object|bool
     */
    public static function getByUserId(int $userId)
    {
        $data = (new Query())
            ->where(['user_id' => $userId])
            ->from(self::collectionName())
            ->one();
        if (!$data) {
            return false;
        }

        $user = Yii::createObject(static::class);
        $user->setAttributes($data, false);
        $user->setIsNewRecord(false);

        return $user;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public static function setOnline(int $userId)
    {
        $user = self::getByUserId($userId);

        if (!$user) {
            $user = new self([
                'user_id' => $userId,
                'time_online' => 0,
                'last_timestamp' => time()
            ]);
        }
        if (($user->is_online ?? false) == false) {
            $user->time_online = 0;
        } else {
            $user->time_online += (time() - (int)$user->last_timestamp);
        }

        $user->last_timestamp = time();
        $user->is_online = true;

        return $user->save();
    }

    /**
     * @param int $userId
     * @return bool
     */
    public static function setOffline(int $userId)
    {
        $user = self::getByUserId($userId);

        if (!$user) {
            $user = new self([
                'user_id' => $userId,
            ]);
        }

        $user->is_online = false;
        $user->time_online = 0;
        $user->last_timestamp = time();

        return $user->save();
    }

    /**
     * @return OnlineUser[]
     */
    public static function getOnlineUsers()
    {
        return self::findAll(['user_id' => 1]);
    }
}