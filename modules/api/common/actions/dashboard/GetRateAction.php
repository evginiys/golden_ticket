<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use yii\rest\Action;

/**
 * Class GetRateAction
 * @package app\modules\api\common\actions\dashboard
 */
class GetRateAction extends Action
{
    /**
     * @return array
     */

    public function run()
    {
        return $this->controller->onSuccess([
            'rur_for_coins' => Payment::RUR_GIVE_FOR_COINS,
            'coins_get' => Payment::COINS_GET_BY_RUR
        ]);
    }
}