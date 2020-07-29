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
 */
class BetAction extends Action
{

    /**
     * @return mixed
     */
    public function run()
    {
        $gameId = Yii::$app->request->post('game_id', 0);
        $ticketId = Yii::$app->request->post('ticket_id', 0);
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $points = Json::decode(Yii::$app->request->post('points'), true);
            $winPoints = [];
            $game = Game::findOne($gameId);
            if (!$game) {
                return $this->controller->onError(Yii::t('app', "Game is not found"), 404);
            }
            if (count($points) != 3) {
                throw new Exception(Yii::t('app', "Incorrect bet"));
            }
            if (Payment::ticketForGame($ticketId, Yii::$app->user->id)) {
                if ($game->status != Game::STATUS_ENDED) {
                    $bets = $game->getGameUsers()->where(['user_id' => Yii::$app->user->id, 'game_id' => $gameId])->count();
                    if ($bets > 0) {
                        throw new Exception(Yii::t('app', "You have already bet"));
                    }
                    $gameCombinations = $game->gameCombinations;
                    foreach ($gameCombinations as $winCombination) {
                        array_push($winPoints, $winCombination->point);
                    }
                    foreach ($points as $point) {
                        $gameUser = new GameUser();
                        $gameUser->game_id = $gameId;
                        $gameUser->user_id = Yii::$app->user->id;
                        $gameUser->point = $point;
                        $gameUser->date_point = date('Y-n-j G:i:s');
                        $gameUser->is_correct = (in_array($point, $winPoints)) ? 1 : 0;
                        if (!$gameUser->save()) {
                            throw new Exception("Error with points");
                        }
                    }
                } else {
                    throw new Exception('Game ended');
                }
                $transaction->commit();
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }
        return $this->controller->onSuccess(['archive' => $game->getArchiveUrl()]);
    }
}