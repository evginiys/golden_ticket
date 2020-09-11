<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\GameUser;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class GetStatisticsAction
 *
 * @package app\modules\api\common\actions\dashboard
 *
 * @SWG\Get(path="/dashboard/get-statistics",
 *     tags={"Dashboard"},
 *     summary="Retrieves user game statistics.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="Success",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(property="allGames", type="integer", example="16", description="Number of games played"),
 *                 @SWG\Property(property="winGames", type="integer", example="8", description="Number of wins")
 *             )
 *         )
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Failed to retrieve user statistics",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class GetStatisticsAction extends Action
{
    /**
     * @return array
     */

    public function run()
    {
        try {
           $userId = Yii::$app->user->id;
           $gamesOfUser = GameUser::numberOfGamesOfUser($userId);
           $winGamesOfUser = GameUser::numberOfWinGamesOfUser($userId);

           return $this->controller->onSuccess([
               'allGames' => $gamesOfUser,
               'winGames' => $winGamesOfUser
           ]);
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }
    }
}