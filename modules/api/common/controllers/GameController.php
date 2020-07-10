<?php

namespace app\modules\api\common\controllers;

use app\models\Game;
use app\models\GameCombination;
use app\models\GameUser;
use app\modules\api\common\actions\game\GetGamesAction;
use app\modules\api\common\actions\ticket\BuyTickets;


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
                'class' => GetGamesAction::class,
                'modelClass' => Game::class
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