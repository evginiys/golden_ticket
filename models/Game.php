<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "game".
 *
 * @property int $id
 * @property int $type
 * @property string $date_start
 * @property float $cost
 * @property float $collected_sum
 * @property string|null $date_end
 * @property int $status
 *
 * @property GameCombination[] $gameCombinations
 * @property GameUser[] $gameUsers
 */
class Game extends ActiveRecord
{
    public const TYPE_REGULAR = 0;
    public const TYPE_JACKPOT = 1;

    public const STATUS_SCHEDULED = 0;
    public const STATUS_IN_PROCESS = 1;
    public const STATUS_ENDED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'game';
    }

    /**
     * @param null|int $key
     * @return array|string
     */
    public static function getTypeDescription($key = null)
    {
        $data = [
            self::TYPE_REGULAR => Yii::t('app', 'Regular'),
            self::TYPE_JACKPOT => Yii::t('app', 'Jackpot'),
        ];

        return $data[$key] ?? $data;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'date_start', 'cost'], 'required'],
            [['type', 'status'], 'integer'],
            [['date_start', 'date_end'], 'safe'],
            [['cost', 'collected_sum'], 'number'],
            [['password'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'date_start' => Yii::t('app', 'Date Start'),
            'cost' => Yii::t('app', 'Cost'),
            'collected_sum' => Yii::t('app', 'Collected Sum'),
            'date_end' => Yii::t('app', 'Date End'),
            'status' => Yii::t('app', 'Status'),
            'password' => Yii::t('app', 'Password'),
        ];
    }

    /**
     * Gets query for [[GameCombinations]].
     *
     * @return ActiveQuery
     */
    public function getGameCombinations()
    {
        return $this->hasMany(GameCombination::class, ['game_id' => 'id']);
    }

    /**
     * Gets query for [[GameUsers]].
     *
     * @return ActiveQuery
     */
    public function getGameUsers()
    {
        return $this->hasMany(GameUser::class, ['game_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function createNewGame()
    {
        $this->status = self::STATUS_SCHEDULED;
        $this->collected_sum = 0;

        return $this->save();
    }
}
