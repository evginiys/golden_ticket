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
class CheckAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $game_id =Yii::$app->request->post('game_id') ;
                $game_user = GameUser::find()->where('user_id',\Yii::$app->user->id)->andWhere('game_id',$game_id)->one();
                if($game_user->is_correct){
                    return $this->controller->onSuccsess(['win'=>true]);
                }else{
                    return $this->controller->onSuccess(['win'=>false]);
                }

    }
}