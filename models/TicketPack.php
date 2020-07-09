<?php

namespace app\models;

use Yii;
use yii\base\Exception;
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
