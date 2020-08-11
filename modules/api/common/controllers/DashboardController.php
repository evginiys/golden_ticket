<?php

namespace app\modules\api\common\controllers;

use app\models\GameUser;
use app\models\User;
use app\modules\api\common\actions\dashboard\ExchangeAction;
use app\modules\api\common\actions\dashboard\GetBalanceAction;
use app\modules\api\common\actions\dashboard\GetRateAction;
use app\modules\api\common\actions\dashboard\GetStatisticsAction;
use app\modules\api\common\actions\dashboard\RefillAction;

/**
 *
 * Class DashboardController
 * @package app\modules\api\common\controllers
 */
class DashboardController extends ApiController
{
    public $modelClass = User::class;

    public function actions()
    {
        return [
            'get-balance' => [
                'class' => GetBalanceAction::class,
                'modelClass' => User::class
            ],
            'get-rate' => [
                'class' => GetRateAction::class,
                'modelClass' => User::class
            ],
            'exchange' => [
                'class' => ExchangeAction::class,
                'modelClass' => User::class
            ],
            'refill' => [
                'class' => RefillAction::class,
                'modelClass' => User::class
            ],
            'get-statistics' => [
                'class' => GetStatisticsAction::class,
                'modelClass' => GameUser::class
            ],
        ];
    }

}
