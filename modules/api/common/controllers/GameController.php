<?php

namespace app\modules\api\common\controllers;

use app\models\Game;
use app\models\GameCombination;
use app\models\GameUser;
use app\modules\api\common\actions\game\ChoosenCombinationAction;
use app\modules\api\common\actions\game\GamesAction;
use app\modules\api\common\actions\game\BetAction;
use app\modules\api\common\actions\game\CheckAction;

/**
 * Class TicketController
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
            'choosenCombination' => [
                'class' => ChoosenCombinationAction::class,
                'modelClass' => GameUser::class
            ],
            'bet' => [
                'class' => BetAction::class,
                'modelClass' => GameUser::class
            ],
            'check' => [
                'class' => checkAction::class,
                'modelClass' => GameUser::class
            ],
        ];
    }
}