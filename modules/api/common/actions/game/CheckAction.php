<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use app\models\GameUser;
use Yii;
use yii\db\Exception;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\ticket
 */
class CheckAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {

        $gameId =\Yii::$app->request->post('game_id') ;
               $gameUser = GameUser::find()->where(['user_id'=>\Yii::$app->user->id])->andWhere(['game_id'=>$gameId])->all();
               if(!$gameUser){
                   return $this->controller->onError('user is not in the game');
               }
                foreach ($gameUser as $one) {
                    if (!$one->is_correct) {
                        return $this->controller->onSuccess(['win' => false]);
                    }
                }
                return $this->controller->onSuccess(['win' => true]);


    }
}