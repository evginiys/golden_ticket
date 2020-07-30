<?php

namespace app\modules\api\common\actions\dashboard;

use yii\rest\Action;

/**
 * Class GetRateAction
 * @package app\modules\api\common\actions\dashboard
 *
 * @SWG\Get(path="/deshboard/get-rate",
 *     tags={"Dashboard"},
 *     summary="Gets the currency exchange rate between the RUR and the coins.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="Information about the exchange rate",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(property="rur_for_coins", type="number", example=100),
 *                 @SWG\Property(property="coins_get", type="number", example=10)
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class GetRateAction extends Action
{
    /**
     * @return array
     */
    public const RUR_GIVE_FOR_COINS = 100;
    public const COINS_GET_BY_RUR = 10;

    public function run()
    {
        return $this->controller->onSuccess([
            'rur_for_coins' => self::RUR_GIVE_FOR_COINS,
            'coins_get' => self::COINS_GET_BY_RUR
        ]);
    }
}