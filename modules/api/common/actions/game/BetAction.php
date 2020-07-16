<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use app\models\GameUser;
use Exception as ExceptionAlias;
use Yii;
use yii\helpers\Json;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\game
 */
class BetAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $gameId = Yii::$app->request->post('game_id', 0);
        $points = Json::decode(Yii::$app->request->post('points'), true);
        try {
            $winPoints = [];
            if (!$game = Game::find($gameId)->where(["<", "date_end", date('Y-n-j G:i:s')])->one()) {
                return $this->controller->onError(Yii::t('app', "Game is not found or game ended"));
            }
            if ($game->status == 1) {
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
                        throw new ExceptionAlias(Yii::t('app', "error with points"));
                    }
                }
            } else {
                throw new ExceptionAlias(Yii::t('app', 'game ended'));
            }
        } catch (ExceptionAlias $e) {
            return $this->controller->onError($e->getMessage());
        }

        return $this->controller->onSuccess(true);
    }
}