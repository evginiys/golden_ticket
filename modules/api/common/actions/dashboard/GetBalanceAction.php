<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use Exception;
use yii\db\Exception as DbException;
use Yii;
use yii\rest\Action;

/**
 * Class GetBalanceAction
 * @package app\modules\api\common\actions\dashboard
 *
 * @SWG\Get(path="/dashboard/get-balance",
 *     tags={"Dashboard"},
 *     summary="Retrieves an information about the balance of current user.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="Information about the balance",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(property="coins", type="number", example=350),
 *                 @SWG\Property(property="coupons", type="number", example=35),
 *                 @SWG\Property(property="tickets", type="integer", example=25)
 *             )
 *         )
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Tickets not found or unable to retrieve data",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class GetBalanceAction extends Action
{
    /**
     * @return array
     */

    public function run()
    {
        try {
            $coins = Yii::$app->user->identity->getBalance(Payment::CURRENCY_COIN);
            $coupons = Yii::$app->user->identity->getBalance(Payment::CURRENCY_COUPON);
            $tickets = Yii::$app->user->identity->getTicketsAmount();
            return $this->controller->onSuccess([
                "coins" => $coins,
                "coupons" => $coupons,
                "tickets" => $tickets
            ]);
        }catch (DbException $e) {
            return $this->controller->onError(Yii::t('app', "Database error"), 400);
        }catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }
    }
}