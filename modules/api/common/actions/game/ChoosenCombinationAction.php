<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use Exception as ExceptionAlias;
use Yii;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\game
 */
class ChoosenCombinationAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $gameId = Yii::$app->request->get('game_id', 0);
        $points = [];
        try {
            if (!$usersInGame = Game::findOne($gameId)->getGameUsers()->select('user_id')->distinct()->count()) {
                return $this->controller->onError(Yii::t('app', "game without users"));
            }
            if (!$points = Game::findOne($gameId)->getGameUsers()->groupBy('user_id , id')->select(['user_id', 'point'])->all()) {
                return $this->controller->onError(Yii::t('app', "no bets"));
            }

        } catch (ExceptionAlias $e) {
            return $this->controller->onError($e->getMessage());
        }

        return $this->controller->onSuccess(['points' => $points, 'usersInGame' => $usersInGame]);
    }
}