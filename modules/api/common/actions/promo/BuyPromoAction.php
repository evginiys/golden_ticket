<?php

namespace app\modules\api\common\actions\promo;

use app\models\Payment;
use app\models\Promo;
use app\models\UserPromoPayment;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class BuyPromoAction
 *
 * @package app\modules\api\common\actions\promo
 *
 * @SWG\Post(path="/promo/buy-promo",
 *     tags={"Promo"},
 *     summary="Performs a buy of promo.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="Success",
 *         @SWG\Schema(ref="#/definitions/Promo")
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Not enough coupons or buy failed",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="Product not found",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class BuyPromoAction extends Action
{
    /**
     * @return mixed
     */
    public function run()
    {
        $promo = Promo::findOne(Yii::$app->request->post('promo_id'));
        if (is_null($promo)) {
            return $this->controller->onError(Yii::t('app', 'Product not found'), 404);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $cost = $promo->cost;
            if (!Yii::$app->user->identity->canPay($cost, Payment::CURRENCY_COUPON)) {
                throw new Exception('Not enough coupons');
            }
            $product = UserPromoPayment::buyPromo(Yii::$app->user->id, $promo->id);
            $transaction->commit();
            return $this->controller->onSuccess($product);
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->controller->onError(Yii::t('app', $e->getMessage(), 400));
        }
    }
}