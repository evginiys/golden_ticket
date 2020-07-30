<?php

namespace app\modules\api\common\actions\game;

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
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="User is not in the game",
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
        $gameUser = GameUser::find()->where(['user_id' => Yii::$app->user->id])->andWhere(['game_id' => Yii::$app->request->post('game_id')])->all();
        if (!$gameUser) {
            return $this->controller->onError(Yii::t('app', 'User is not in game'), 404);
        }
        $points = 3;
        foreach ($gameUser as $one) {
            if (!$one->is_correct) {
                $points--;
            }
        }
        if ($points != 3) {
            return $this->controller->onSuccess(['win' => false, 'matches' => $points]);
        } else
            return $this->controller->onSuccess(['win' => true, 'matches' => 3]);
    }
}