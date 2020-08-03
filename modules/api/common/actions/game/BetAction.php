<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use app\models\GameUser;
use app\models\Payment;
use Exception;
use Yii;
use yii\helpers\Json;
use yii\rest\Action;

/**
 * Class BetAction
 * @package app\modules\api\common\actions\game
 *
 * @SWG\Post(path="/game/bet",
 *     tags={"Game"},
 *     summary="Makes a bet in the game and returns a link to the password-protected ZIP-archive containing a text file with game combinations.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="game_id",
 *         type="integer",
 *         default=0,
 *         description="ID of game to bet in"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="points",
 *         type="string",
 *         required=true,
 *         description="Array of points to bet on"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Bet successful",
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
 *     @SWG\Response(
 *         response=400,
 *         description="The bet is incorrect or the bet is made already or error with points",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="Game is not found",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class BetAction extends Action
{

    /**
     * @return mixed
     */
    public function run()
    {
        $gameId = Yii::$app->request->post('game_id', 0);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $points = Json::decode(Yii::$app->request->post('points'), true);
            $winPoints = [];
            $game = Game::findOne($gameId);
            if (!$game) {
                return $this->controller->onError(Yii::t('app', "Game is not found"), 404);
            }
            if (count($points) != Game::COUNT_POINT) {
                throw new Exception(Yii::t('app', "Incorrect bet"));
            }
            if ($game->status != Game::STATUS_ENDED) {
                if (Payment::betByTicket($gameId, Yii::$app->user->id)) {
                    $bets = $game->getGameUsers()
                        ->where(['user_id' => Yii::$app->user->id, 'game_id' => $gameId])
                        ->count();
                    if ($bets) {
                        throw new Exception(Yii::t('app', "You have already bet"));
                    }
                    $gameCombinations = $game->gameCombinations;
                    foreach ($gameCombinations as $winCombination) {
                        $winPoints[] = $winCombination->point;
                    }
                    foreach ($points as $point) {
                        $gameUser = new GameUser();
                        $gameUser->game_id = $gameId;
                        $gameUser->user_id = Yii::$app->user->id;
                        $gameUser->point = $point;
                        $gameUser->date_point = date('Y-m-d H:i:s');
                        $gameUser->is_correct = (in_array($point, $winPoints)) ? 1 : 0;
                        if (!$gameUser->save()) {
                            throw new Exception("Error with points");
                        }
                    }
                } else {
                    throw new Exception('Cannot bet');
                }
            } else {
                throw new Exception('Game ended');
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }
        return $this->controller->onSuccess(['archive' => $game->getArchiveUrl()]);
    }
}