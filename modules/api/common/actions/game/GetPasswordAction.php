<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use Yii;
use yii\rest\Action;

/**
 * Class GetPasswordAction
 * @package app\modules\api\common\actions\game
 *
 * @SWG\Get(path="/game/get-password",
 *     tags={"Game"},
 *     summary="Gets the password for ZIP-archive containing a text file with game combinations.",
 *     description="Note that the password is only available when the game *is over* (ended).",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="query",
 *         name="gameId",
 *         type="integer",
 *         required=true,
 *         description="ID of the game to get password for"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="The password for the ZIP-archive",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(property="password", type="string", example="PASSWORD")
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="Game is not over",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class GetPasswordAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $gameId = Yii::$app->request->get('game_id');
        $game = Game::find($gameId)->where(['status' => Game::STATUS_ENDED])->one();
        if ($game) {
            $password = $game->getArchivePassword();
            return $this->controller->onSuccess(['password' => $password]);
        } else {
            return $this->controller->onError(Yii::t('app', "Game is not ended"), 404);
        }
    }
}