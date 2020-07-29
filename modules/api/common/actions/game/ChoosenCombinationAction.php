<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
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
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
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
            if (!$usersInGame = Game::findOne($gameId)->getGameUsers()->select('user_id')->distinct()->count()) {
                return $this->controller->onError(Yii::t('app', "Game without users"), 404);
            }
            if (!$points = Game::findOne($gameId)->getGameUsers()->groupBy('user_id , id')->select(['user_id', 'point'])->all()) {
                return $this->controller->onError(Yii::t('app', "No bets"), 404);
            }

        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app',$e->getMessage()), 400);
        }

        return $this->controller->onSuccess(['points' => $points, 'usersInGame' => $usersInGame]);
    }
}