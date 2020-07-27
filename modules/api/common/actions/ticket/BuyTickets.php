<?php

namespace app\modules\api\common\actions\ticket;

use app\models\TicketPack;
use Yii;
use yii\base\Exception;
use yii\rest\Action;

/**
 * Class BuyTickets
 * @package app\modules\api\common\actions\ticket
 */
class BuyTickets extends Action
{
    /**
     * @return mixed
     */
    public function run()
    {
        try {
            $ticketPack = TicketPack::findOne(Yii::$app->request->post('ticket_pack_id', 0));
            if (!$ticketPack) {
                throw new Exception('Ticket pack is not found');
            }
            $ticketPack->sell(Yii::$app->user->id, Yii::$app->request->post('amount', 0));
        } catch (\Exception $e) {
            return $this->controller->onError($e->getMessage(),400);
        }

        return $this->controller->onSuccess(true);
    }
}