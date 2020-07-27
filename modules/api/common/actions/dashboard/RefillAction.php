<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use Yii;
use yii\db\Exception;
use yii\rest\Action;

/**
 * Class RefillAction
 * @package app\modules\api\common\actions\dashboard
 */
class RefillAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        try {
            if ($amount = Yii::$app->request->post('amount')) {
                if (Payment::refill(Yii::$app->user->id, $amount)) {
                    return $this->controller->onSuccess(true);
                } else {
                    throw new Exception("Cannot refill wallet");
                }
            } else {
                throw new Exception( "Cannot refill wallet");
            }
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app',$e->getMessage()), 400);
        }
    }
}