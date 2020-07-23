<?php

namespace app\models;


use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property float $amount
 * @property int $currency
 * @property int|null $to_user_id
 * @property int|null $from_user_id
 * @property int|null $ticket_id
 * @property int $type
 * @property int $status
 * @property string|null $comment
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $fromUser
 * @property Ticket $ticket
 * @property User $toUser
 */
class Payment extends ActiveRecord
{
    public const TYPE_BUY = 0;
    public const TYPE_CHARGE = 1;
    public const STATUS_NEW = 1;
    public const STATUS_DONE = 2;
    public const CURRENCY_RUR = 0;
    public const CURRENCY_COUPON = 1;

    /**
     * @param Ticket[] $tickets
     * @param int $userId
     */
    public static function buyTickets(array $tickets, int $userId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($tickets as $ticket) {
                $payment = new self([
                    'amount' => $ticket->cost,
                    'from_user_id' => $userId,
                    'type' => self::TYPE_BUY,
                    'status' => self::STATUS_DONE,
                    'currency' => self::CURRENCY_RUR,
                    'comment' => 'Платеж за билеты',
                    'ticket_id' => $ticket->id
                ]);
                if (!$payment->save()) {
                    throw new Exception(Json::encode($payment->getErrors()));
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param int $userId
     * @return array
     */
    public static function getUserBalance(int $userId)
    {
        return self::find()
            ->select([
                new Expression('SUM(COALESCE(plus.amount, 0)) - SUM(COALESCE(minus.amount, 0)) AS balance'),
                'p.currency'
            ])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(self::tableName() . ' AS plus', 'p.id=plus.id AND plus.type=:plus_type AND plus.to_user_id=:user_id')
            ->leftJoin(self::tableName() . ' AS minus', 'p.id=minus.id AND minus.type=:minus_type AND minus.from_user_id=:user_id')
            ->groupBy('p.currency')
            ->asArray()
            ->orderBy(['p.currency' => SORT_DESC])
            ->params([
                'plus_type' => self::TYPE_CHARGE,
                'minus_type' => self::TYPE_BUY,
                'user_id' => $userId
            ])
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     *
     * @param $userId
     * @return int
     * @throws Exception
     */
    public static function userTickets(int $userId)
    {
        $numberOfTickets = 0;
        try {
            if ($payments = self::find()->where(['from_user_id' => $userId])->with('ticket')->all()) {
                foreach ($payments as $payment) {
                    if ($payment->ticket->is_active == 1) {
                        $numberOfTickets++;
                    }
                }
            } else {
                throw new Exception(Yii::t('app', 'not found tickets'));
            }
        } catch (Exception $e) {
            throw new Exception(Yii::t('app', $e->getMessage()));
        }
        return $numberOfTickets;
    }

    /**
     * @param $userId
     * @param $amount
     * @return bool
     * @throws Exception
     */
    public static function refill(int $userId, float $amount)
    {
        try {
            $payment = new self([
                'currency' => self::CURRENCY_RUR,
                'status' => self::STATUS_NEW,
                'amount' => $amount,
                'to_user_id' => $userId,
                'type' => self::TYPE_CHARGE,
                'comment' => "refill rur"
            ]);
            if (!$payment->validate() || !$payment->save()) {
                throw  new Exception(Yii::t('app', "cannot refill wallet"));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param $userId
     * @param $coins
     * @param $coupons
     * @return bool
     * @throws Exception
     */
    public static function coinsToCoupon(int $userId, float $coins, float $coupons)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $sell = new self([
                'status' => self::STATUS_DONE,
                'currency' => self::CURRENCY_RUR,
                'type' => self::TYPE_BUY,
                'comment' => 'обмен на купоны',
                'amount' => $coins,
                'from_user_id' => $userId
            ]);
            if (!$sell->validate() || !$sell->save()) throw new Exception(Yii::t('app', 'cannot exchange'));
            $buy = new self([
                'status' => self::STATUS_DONE,
                'currency' => self::CURRENCY_COUPON,
                'type' => self::TYPE_CHARGE,
                'comment' => 'покупка купонов',
                'amount' => $coupons,
                'to_user_id' => $userId
            ]);
            if (!$buy->validate() || !$buy->save()) {
                throw new Exception(Yii::t('app', 'cannot exchange'));
            }
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw new Exception($e->getMessage());
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'type', 'status'], 'required'],
            [['amount'], 'number'],
            [['currency', 'to_user_id', 'from_user_id', 'ticket_id', 'type', 'status'], 'integer'],
            [['comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['from_user_id' => 'id']],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::class, 'targetAttribute' => ['ticket_id' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['to_user_id' => 'id']],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'to_user_id' => 'To User ID',
            'from_user_id' => 'From User ID',
            'ticket_id' => 'Ticket ID',
            'type' => 'Type',
            'status' => 'Status',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[FromUser]].
     *
     * @return ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['id' => 'ticket_id']);
    }

    /**
     * Gets query for [[ToUser]].
     *
     * @return ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id']);
    }

}
