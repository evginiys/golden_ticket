<?php

namespace app\modules\api\common\actions\ticket;

use app\models\Ticket;
use yii\rest\Action;

/**
 * Class GetTicketsAction
 * @package app\modules\api\common\actions\ticket
 *
 * @SWG\Get(path="/ticket/tickets",
 *     tags={"Ticket"},
 *     summary="Retrieves a list of tickets",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="List of tickets",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="array",
 *                 @SWG\Items(ref="#/definitions/Ticket")
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 *
 */
class GetTicketsAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $data = Ticket::find()->joinWith('ticketPack')->all();

        return $this->controller->onSuccess($data);
    }
}