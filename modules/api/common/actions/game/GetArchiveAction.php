<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use Yii;
use yii\rest\Action;

/**
 * Class GetArchiveAction
 * @package app\modules\api\common\actions\game
 *
 * @SWG\Get(path="/game/get-archive",
 *     tags={"Game"},
 *     summary="Gets URL to the password-protected ZIP-archive containing a text file with game combinations.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="query",
 *         name="gameId",
 *         type="string",
 *         required=true,
 *         description="ID of the game to get archive for"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Success",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(property="archive", type="string", example="combination_1593598500.zip")
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="Game is not found",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class GetArchiveAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $gameId = Yii::$app->request->get('gameId');
        $game = Game::findOne($gameId);
        if ($game) {
            $archive = $game->getArchiveUrl();
            return $this->controller->onSuccess(['archive' => $archive]);
        } else {
            return $this->controller->onError(Yii::t('app', "Game is not found"), 404);
        }
    }
}