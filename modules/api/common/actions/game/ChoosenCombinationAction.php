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
 *
 * @SWG\Get(path="/game/choosen-combination",
 *     tags={"Game"},
 *     summary="Retrieves bets and count of players in the game.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="query",
 *         name="game_id",
 *         type="integer",
 *         default=0,
 *         description="ID of the game"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Information about players and bets",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(
 *                     property="points",
 *                     type="array",
 *                     @SWG\Items(
 *                         type="object",
 *                         @SWG\Property(property="user_id", type="integer", example=1),
 *                         @SWG\Property(property="point", type="integer", example=1)
 *                     )
 *                 ),
 *                 @SWG\Property(property="usersInGame", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Error",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="One of the following errors: game is not found, game without users, no bets",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
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
                return $this->controller->onError(Yii::t('app', "Game is not found"), 404);
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