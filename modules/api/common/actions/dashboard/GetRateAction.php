<?php

namespace app\modules\api\common\actions\dashboard;

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
    public const RUR_GIVE_FOR_COINS = 100;
    public const COINS_GET_BY_RUR = 10;

    public function run()
    {
        return $this->controller->onSuccess(['rur_for_coins' => self::RUR_GIVE_FOR_COINS, 'coins_get' => self::COINS_GET_BY_RUR]);
    }
}