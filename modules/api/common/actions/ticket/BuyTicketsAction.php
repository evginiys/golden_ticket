<?php

namespace app\modules\api\common\actions\ticket;

use app\models\TicketPack;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class BuyTicketsAction
 * @package app\modules\api\common\actions\ticket
 *
 * @SWG\Post(path="/ticket/buy",
 *     tags={"Ticket"},
 *     summary="Performs a buy of tickets from the ticket pack.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="ticket_pack_id",
 *         type="integer",
 *         default=0,
 *         description="ID of the ticket pack to buy a ticket from"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="amount",
 *         type="integer",
 *         default=0,
 *         description="Amount of tickets to buy"
 *     ),
 *     @SWG\Response(response=200, ref="#/responses/success_simple"),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=400,
 *         description="One of the following errors: ticket pack is not found, amount is incorrect, not enough funds in account",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class BuyTicketsAction extends Action
{
    /**
     * @return mixed
     */
    public function run()
    {
        try {
            $ticketPack = TicketPack::findOne(Yii::$app->request->post('ticket_pack_id', 0));
            if (!$ticketPack) {
                return $this->controller->onError(Yii::t('app', 'Ticket pack is not found'), 404);
            }
            $amount=Yii::$app->request->post('amount', 0);
            if(!is_numeric($amount)){
                return $this->controller->onError(Yii::t('app', 'Incorrect amount'), 400);
            }
            $ticketPack->sell(Yii::$app->user->id, Yii::$app->request->post('amount', $amount));
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }

        return $this->controller->onSuccess(true);
    }
}