<?php

namespace app\modules\api\common\controllers;

use app\models\Game;
use app\models\GameCombination;
use app\models\GameUser;
use app\modules\api\common\actions\game\BetAction;
use app\modules\api\common\actions\game\CheckAction;
use app\modules\api\common\actions\game\ChoosenCombinationAction;
use app\modules\api\common\actions\game\GamesAction;
use app\modules\api\common\actions\game\GetArchiveAction;
use app\modules\api\common\actions\game\GetPasswordAction;
use app\modules\api\common\actions\OnlineCounterAction;

/**
 * Class GameController
 * @package app\modules\api\common\controllers
 */
class GameController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = Game::class;

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'games' => [
                'class' => GamesAction::class,
                'modelClass' => Game::class
            ],
            'choosen-combination' => [
                'class' => ChoosenCombinationAction::class,
                'modelClass' => GameCombination::class
            ],
            'bet' => [
                'class' => BetAction::class,
                'modelClass' => GameUser::class
            ],
            'check' => [
                'class' => CheckAction::class,
                'modelClass' => GameUser::class
            ],
            'online-count' => [
                'class' => OnlineCounterAction::class,
                'modelClass' => GameUser::class
            ],
            'get-password' => [
                'class' => GetPasswordAction::class,
                'modelClass' => Game::class
            ],
            'get-archive' => [
                'class' => GetArchiveAction::class,
                'modelClass' => Game::class
            ]
        ];
    }
}