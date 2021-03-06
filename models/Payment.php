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

    public const CURRENCY_COIN = 0;
    public const CURRENCY_COUPON = 1;
    public const CURRENCY_RUR = 2;

    public const RUR_FOR_COINS = 1;
    public const COINS_FOR_COUPON = 10;
    public const COINS_FOR_RUR = 1;

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
                    'currency' => self::CURRENCY_COIN,
                    'comment' => 'Pay for tickets',
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
     * @param int $gameId
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public static function betForRegularGame(int $gameId, int $userId): bool
    {
        $game = Game::findOne($gameId);
        if (!$game or $game->type != Game::TYPE_REGULAR) {
            throw new Exception("Not found game");
        }

        $tickets = Ticket::find()->where(['cost' => $game->cost])->select('id')->all();
        if (!$tickets) {
            throw new Exception("Not found tickets");
        }
        $betTicketId = -1;
        foreach ($tickets as $ticket) {
            if (Payment::hasTicket($ticket->id, $userId)) {
                $betTicketId = $ticket->id;
                break;
            }
        }
        if ($betTicketId == -1) {
            throw new Exception("No ticket to bet");
        }
        $payment = new self([
            'amount' => 0,
            'to_user_id' => $userId,
            'type' => self::TYPE_CHARGE,
            'status' => self::STATUS_DONE,
            'currency' => self::CURRENCY_COIN,
            'comment' => 'Ticket for game',
            'ticket_id' => $betTicketId
        ]);
        if (!$payment->save()) {
            throw new Exception($payment->getErrors());
        }

        return true;
    }

    /**
     * @param int $ticketId
     * @param int $userId
     * @return bool
     */
    public static function hasTicket(int $ticketId, int $userId): bool
    {
        $ticketsIn = Payment::find()
            ->where(['ticket_id' => $ticketId, 'type' => self::TYPE_BUY, 'from_user_id' => $userId])
            ->count();
        $ticketsOut = Payment::find()
            ->where(['ticket_id' => $ticketId, 'type' => self::TYPE_CHARGE, 'to_user_id' => $userId])
            ->count();
        if ($ticketsIn > $ticketsOut) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $gameId
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public static function betForJackpotGame(int $gameId, int $userId): bool
    {
        $game = Game::findOne($gameId);
        if (!$game or $game->type != Game::TYPE_JACKPOT) {
            throw new Exception("Not found game");
        }
        $ticketPack = TicketPack::getTicketPackByCost($game->cost);
        $tickets = $ticketPack->tickets;
        if (!$tickets) {
            throw new Exception("Not found tickets");
        }
        $betTicketAmount = 0;
        foreach ($tickets as $ticket) {
            while (Payment::hasTicket($ticket->id, $userId)) {
                $payment = new self([
                    'amount' => 0,
                    'to_user_id' => $userId,
                    'type' => self::TYPE_CHARGE,
                    'status' => self::STATUS_DONE,
                    'currency' => self::CURRENCY_COIN,
                    'comment' => 'Ticket for jackpot game',
                    'ticket_id' => $ticket->id
                ]);
                if (!$payment->save()) {
                    throw new Exception($payment->getErrors());
                } else {
                    $betTicketAmount++;
                    if ($betTicketAmount == TicketPack::AMOUNT_OF_TICKETS) {
                        break 2;
                    }
                }
            }
        }
        if ($betTicketAmount != TicketPack::AMOUNT_OF_TICKETS) {
            throw new Exception("Not found full ticket pack");
        }

        return true;
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
     * @param $userId
     * @param $amount
     * @return bool
     * @throws Exception
     */
    public static function refill(int $userId, float $amount)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $getCoins = new self([
                'currency' => self::CURRENCY_COIN,
                'status' => self::STATUS_NEW,
                'amount' => $amount * (1 / self::RUR_FOR_COINS),
                'to_user_id' => $userId,
                'type' => self::TYPE_CHARGE,
                'comment' => "Refill COIN"
            ]);
            if (!$getCoins->save()) {
                throw  new Exception("Cannot refill wallet");
            }
            $payRur = new self([
                'currency' => self::CURRENCY_RUR,
                'status' => self::STATUS_NEW,
                'amount' => $amount,
                'from_user_id' => $userId,
                'type' => self::TYPE_BUY,
                'comment' => "Pay rur for COIN"
            ]);
            if (!$payRur->save()) {
                throw  new Exception("Cannot refill wallet");
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw  $e;
        }
        return true;
    }

    /**
     * @param int $userId
     * @param int $cost
     * @return int
     * @throws Exception
     */
    public static function payForPromo(int $userId, int $cost): int
    {
        $payment = new self([
            'currency' => self::CURRENCY_COUPON,
            'status' => self::STATUS_NEW,
            'amount' => $cost,
            'from_user_id' => $userId,
            'type' => self::TYPE_BUY,
            'comment' => "Pay coupons for promo"
        ]);
        if (!$payment->save()) {
            throw new Exception(Json::encode($payment->getErrors()));
        }
        return $payment->id;
    }

    /**
     * @param $userId
     * @param $coupons
     * @param $coins
     * @return bool
     * @throws Exception
     */
    public static function coinsToCoupon(int $userId, int $coupons, float $coins): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $coinsCalculated = $coupons * (self::COINS_FOR_COUPON);
            if ($coins != $coinsCalculated) {
                throw new Exception(Yii::t('app', 'The number of coins provided must be equal to the calculated cost'));
            }
            $userCoins = User::findOne($userId)->getBalance(Payment::CURRENCY_COIN);
            if ($userCoins < $coinsCalculated) {
                throw new Exception(Yii::t('app', 'Not enough coins'));
            }
            $sell = new self([
                'status' => self::STATUS_DONE,
                'currency' => self::CURRENCY_COIN,
                'type' => self::TYPE_BUY,
                'comment' => 'Exchange on coupons',
                'amount' => $coinsCalculated,
                'from_user_id' => $userId
            ]);
            if (!$sell->save()) {
                throw new Exception(Yii::t('app', 'Cannot exchange'));
            }
            $buy = new self([
                'status' => self::STATUS_DONE,
                'currency' => self::CURRENCY_COUPON,
                'type' => self::TYPE_CHARGE,
                'comment' => 'Buy coupons',
                'amount' => $coupons,
                'to_user_id' => $userId
            ]);
            if (!$buy->save()) {
                throw new Exception(Yii::t('app', '??annot exchange'));
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
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
