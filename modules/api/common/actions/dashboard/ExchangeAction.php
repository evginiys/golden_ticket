<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use Yii;
use yii\db\Exception;
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
            if (Yii::$app->user->identity->canPay($coins = Yii::$app->request->post('coins')) &&
                Yii::$app->request->post('coupons') >= 0
                && $coupons = Yii::$app->request->post('coupons')) {
                Payment::CoinsToCoupon(Yii::$app->user->id, $coins, $coupons);
            } else {
                return $this->controller->onError(Yii::t('app', 'not enough coins'));
            }
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()));
        }
        return $this->controller->onSuccess(['done' => true]);
    }
}