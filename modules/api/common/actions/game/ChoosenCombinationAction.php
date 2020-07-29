<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use app\models\GameUser;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class ChoosenCombinationAction
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
            $game = Game::findOne($gameId);
            if (!$game) {
                throw new Exception('Game is not found');
            }
            $gameUsersDistinctUser = $game->getGameUsers()->select('user_id')->distinct();
            $usersInGame = $gameUsersDistinctUser->count();
            if (!$usersInGame) {
                return $this->controller->onError(Yii::t('app', "Game without users"), 404);
            }
            $usersId = $gameUsersDistinctUser->all();
            if (!$usersId) {
                return $this->controller->onError(Yii::t('app', "No bets"), 404);
            }
            foreach ($usersId as $userId) {
                $userPoints = GameUser::find()
                    ->select('point')
                    ->where([
                        'user_id' => $userId,
                        'game_id' => $gameId
                    ])
                    ->asArray()
                    ->all();
                $points[] = $userPoints;
            }

        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }

        return $this->controller->onSuccess(['points' => $points, 'usersInGame' => $usersInGame]);
    }
}