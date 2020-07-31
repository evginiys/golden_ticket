<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use yii\rest\Action;

/**
 * Class GamesAction
 * @package app\modules\api\common\actions\game
 *
 * @SWG\Get(path="/game/games",
 *     tags={"Game"},
 *     summary="Retrieves a list of scheduled games and games in process.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="List of games",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="array",
 *                 @SWG\Items(ref="#/definitions/Game")
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class GamesAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $data = Game::find()
            ->where(['status' => [Game::STATUS_SCHEDULED, Game::STATUS_IN_PROCESS]])
            ->select(['id', 'type', 'cost', 'status', 'date_start', 'date_end'])
            ->all();
        return $this->controller->onSuccess($data);
    }
}