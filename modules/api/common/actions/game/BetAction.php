<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use app\models\GameUser;
use Yii;
use yii\db\Exception;
use yii\helpers\Json;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\ticket
 */
class BetAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {

        $gameId =\Yii::$app->request->post('game_id');
        $points =Json::decode(\Yii::$app->request->post('points'),true);

            try {
                $winPoints=[];
                if(!$game=Game::find($gameId)->one()){
                    \Yii::t('app',"Game is not found");
                }
                if( $game->status==1) {
                    $gameCombinations = $game->gameCombinations;
                    foreach ($gameCombinations as $k=>$v){
                        array_push($winPoints,$v->point);
                    }
                    foreach ($points as $k=>$v) {
                        $gameUser = new GameUser();
                        $gameUser->game_id = $gameId;
                        $gameUser->user_id = \Yii::$app->user->id;
                        $gameUser->point = $v;
                        $gameUser->date_point = date('Y-n-j G:i:s');

                    $gameUser->is_correct =(in_array($v,$winPoints))?1:0;
                    if (!$gameUser->save()) {
                        throw new Exception("error with points");
                    }
                }
            } else {
                throw new Exception('game ended');
            }
        } catch (\Exception $e) {
            return $this->controller->onError($e->getMessage());
        }

        return $this->controller->onSuccess(true);
    }
}