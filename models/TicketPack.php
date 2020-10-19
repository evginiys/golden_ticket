<?php

namespace app\models;

use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ticket_pack".
 *
 * @property int $id
 * @property string $name
 * @property int $is_active
 *
 * @property Ticket[] $tickets
 */
class TicketPack extends ActiveRecord
{

    public const AMOUNT_OF_TICKETS = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket_pack';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_active'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['ticket_pack_id' => 'id']);
    }

    /**
     * @param int $cost
     * return TicketPack
     */
    public static function getTicketPackByCost(int $cost): TicketPack
    {
        $ticket = Ticket::find()->where(['cost' => $cost / TicketPack::AMOUNT_OF_TICKETS])->one();
        if (!$ticket) {
            throw new Exception(Yii::t('app', 'Not found pack'));
        }
        return $ticket->ticketPack;
    }

    /**
     * @param int $userId
     * @param float $amount
     * @return bool
     * @throws Exception
     */
    public function sell(int $userId, float $amount): bool
    {
        $amountSum = array_sum(ArrayHelper::getColumn($this->tickets, 'cost'));

        if ((float)$amountSum > (float)$amount) {
            throw new Exception('Amount is incorrect');
        }
        if (!User::findOne($userId)->canPay($amountSum)) {
            throw new Exception('There are no enough funds in your account');
        }

        Payment::buyTickets($this->tickets, $userId);

        return true;
    }
}
