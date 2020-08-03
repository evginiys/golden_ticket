<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class ExchangeAction
 * @package app\modules\api\common\actions\dashboard
 *
 * @SWG\Post(path="/dashboard/exchange",
 *     tags={"Dashboard"},
 *     summary="Performs an exchange of coins for coupons for current user.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="coins",
 *         type="number",
 *         required=true,
 *         description="Amount of coins to exchange"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="coupons",
 *         type="number",
 *         required=true,
 *         description="Amount of coupons to get"
 *     ),
 *     @SWG\Response(response=200, ref="#/responses/success_simple"),
 *     @SWG\Response(
 *         response=400,
 *         description="One of the following errors: not enough coins, incorrect coupons amount, cannot perform exchange",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class ExchangeAction extends Action
{
    /**
     * @return mixed
     */

    public function run()
    {
        try {
            $coupons = Yii::$app->request->post('coupons');
            if (!is_numeric($coupons) || !is_int(+$coupons)) {
                return $this->controller->onError(Yii::t('app', 'Incorrect amount of coupons'), 400);
            }
            if ((int)$coupons >= 0) {
                Payment::coinsToCoupon(Yii::$app->user->id, (int)$coupons);
            } else {
                return $this->controller->onError(Yii::t('app', 'Incorrect amount of coupons'), 400);
            }
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }
        return $this->controller->onSuccess(true);
    }
}