<?php

namespace app\modules\api\common\actions\ticket;

use app\models\TicketPack;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\ticket
 *
 * @SWG\Get(path="/ticket/packs",
 *     tags={"Ticket"},
 *     summary="Retrieves a list of ticket packs.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="List of ticket packs",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="array",
 *                 @SWG\Items(ref="#/definitions/TicketPack")
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class GetPacksAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $data = TicketPack::find()->all();

        return $this->controller->onSuccess($data);
    }
}