<?php

namespace app\models;

use app\models\behaviors\MongoLogger;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "game_user".
 *
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property int $point
 * @property string $date_point
 * @property int|null $is_correct
 *
 * @property Game $game
 * @property User $user
 */
class GameUser extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'game_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'game_id', 'point', 'date_point'], 'required'],
            [['user_id', 'game_id', 'point', 'is_correct'], 'integer'],
            [['date_point'], 'safe'],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => Game::class, 'targetAttribute' => ['game_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'game_id' => Yii::t('app', 'Game ID'),
            'point' => Yii::t('app', 'Point'),
            'date_point' => Yii::t('app', 'Date Point'),
            'is_correct' => Yii::t('app', 'Is Correct'),
        ];
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
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
