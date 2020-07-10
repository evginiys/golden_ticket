<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use app\models\GameUser;
use yii\db\Exception;
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
        $game_id =Yii::$app->request->post('game_id') ;
        $points =array_sum(json_encode(Yii::$app->request->post('points')));

            try {
                if(Game::find($game_id)->one()->status) {
                    $game_user = new GameUser();
                    $game_user->game_id = $game_id;
                    $game_user->user_id = \Yii::$app->user->id;
                    $game_user->point = $points;
                    $game_user->date_point = date('Y-n-j G:i:s');
                    $game_user->is_correct = ($points == Game::find($game_id)->one()->collected_sum) ? 1 : 0;
                    if(!$game_user->save()){
                        throw new Exception('error with points');
                    }
                }else{
                    throw new Exception('game ended');
                }
            }catch (\Exception $e){
                return $this->controller->onError($e->getMessage());
            }

            return $this->controller->onSuccess(true);
        }
}