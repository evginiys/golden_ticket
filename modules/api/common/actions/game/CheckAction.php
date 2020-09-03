<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use app\models\GameUser;
use Yii;
use yii\rest\Action;

/**
 * Class CheckAction
 * @package app\modules\api\common\actions\game
 *
 * @SWG\Post(path="/game/check",
 *     tags={"Game"},
 *     summary="Checks whether the current user won the game.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="game_id",
 *         type="integer",
 *         required=true,
 *         description="ID of the game to check"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Win status and count of point matches",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(property="win", type="boolean", example=true),
 *                 @SWG\Property(property="matches", type="integer", minimum=0, maximum=3, example=3)
 *             )
 *         )
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Game is not ended",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="Game is not found or user is not in the game",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class CheckAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $game = Game::findOne(Yii::$app->request->post('game_id'));
        if (!$game) {
            return $this->controller->onError(Yii::t('app', 'Game is not found'), 404);
        }
        if ($game->status != Game::STATUS_ENDED) {
            return $this->controller->onError(Yii::t('app', 'Game is not ended'), 400);
        }
        $gameUser = GameUser::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->andWhere(['game_id' => $game->id])
            ->all();
        if (!$gameUser) {
            return $this->controller->onError(Yii::t('app', 'User is not in game'), 404);
        }
        $points = Game::COUNT_POINT;
        foreach ($gameUser as $one) {
            if (!$one->is_correct) {
                $points--;
            }
        }
        if ($points != Game::COUNT_POINT) {
            return $this->controller->onSuccess(['win' => false, 'matches' => $points]);
        } else
            return $this->controller->onSuccess(['win' => true, 'matches' => Game::COUNT_POINT]);
    }
}