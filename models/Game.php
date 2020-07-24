<?php

namespace app\models;

use app\modules\generator\Generator;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;

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
 * @property string|null $password
 * @property string|null $archive_url
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
     * @param null|int $key
     * @return array|string
     */
    public static function getStatusDescription($key = null)
    {
        $data = [
            self::STATUS_SCHEDULED => Yii::t('app', 'Scheduled'),
            self::STATUS_IN_PROCESS => Yii::t('app', 'In Process'),
            self::STATUS_ENDED => Yii::t('app', 'Ended'),
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
            'password' => Yii::t('app', 'Archive Password Hash'),
            'archive_url' => Yii::t('app', 'Archive URL'),
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
     * @throws Exception
     */
    public function createNewGame()
    {
        $this->status = self::STATUS_SCHEDULED;
        $this->collected_sum = 0;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            $generator = new Generator();
            $combination = $generator->getCombination();
            foreach($combination as $point) {
                $gc = new GameCombination([
                    'game_id' => $this->id,
                    'point' => $point
                ]);
                if (!$gc->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }

            $encryptedData = $generator->encryptData();

            $this->archive_url = $encryptedData['archive'];
            $this->password = $encryptedData['hash'];
            $this->save(false);

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Gets decrypted password for archive with combination.
     *
     * @return string
     */
    public function getArchivePassword() {
        return (new Generator())->decryptData($this->password);
    }

    /**
     * Gets download link for an archive.
     *
     * @return string
     */
    public function getArchiveUrl() {
        return Url::to('@web/uploads' . DIRECTORY_SEPARATOR . $this->archive_url, true);
    }
}
