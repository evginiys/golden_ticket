<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class ExchangeAction
 * @package app\modules\api\common\actions\dashboard
 */
class ExchangeAction extends Action
{
    /**
     * @return mixed
     */

    public function run()
    {
        try {
            $coins = Yii::$app->request->post('coins');
            $coupons = Yii::$app->request->post('coupons');
            if (Yii::$app->user->identity->canPay($coins) &&
                $coupons >= 0) {
                Payment::coinsToCoupon(Yii::$app->user->id, $coins, $coupons);
            } else {
                return $this->controller->onError(Yii::t('app', 'Not enough coins'), 400);
            }
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }
        return $this->controller->onSuccess(['done' => true]);
    }
}