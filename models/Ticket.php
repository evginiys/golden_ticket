<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ticket".
 *
 * @property int $id
 * @property int $ticket_pack_id
 * @property float $cost
 * @property int $is_active
 *
 * @property TicketPack $ticketPack
 */
class Ticket extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_pack_id', 'cost'], 'required'],
            [['ticket_pack_id', 'is_active'], 'integer'],
            [['cost'], 'number'],
            [['ticket_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => TicketPack::class, 'targetAttribute' => ['ticket_pack_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_pack_id' => 'Ticket Pack ID',
            'cost' => 'Cost',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[TicketPack]].
     *
     * @return ActiveQuery
     */
    public function getTicketPack()
    {
        return $this->hasOne(TicketPack::class, ['id' => 'ticket_pack_id']);
    }
}
