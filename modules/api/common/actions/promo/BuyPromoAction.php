<?php

namespace app\modules\api\common\actions\promo;

use app\models\Payment;
use app\models\Promo;
use app\models\UserPromoPayment;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class BUYPromoAction
 * @package app\modules\api\common\actions\promo
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