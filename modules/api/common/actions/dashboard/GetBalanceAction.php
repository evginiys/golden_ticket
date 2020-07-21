<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class GetBalanceAction
 * @package app\modules\api\common\actions\dashboard
 */
class GetBalanceAction extends Action
{
    /**
     * @return array
     */

    public function run()
    {
        try {
            $currency = Yii::$app->request->post('currency');
            $coins = Yii::$app->user->identity->getBalance(Payment::CURRENCY_RUR);
            $coupons = Yii::$app->user->identity->getBalance(Payment::CURRENCY_COUPON);
            $tickets = Payment::userTickets(Yii::$app->user->id);
            return $this->controller->onSuccess(["coins" => $coins, "coupons" => $coupons, "tickets" => $tickets]);
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()));
        }
    }
}