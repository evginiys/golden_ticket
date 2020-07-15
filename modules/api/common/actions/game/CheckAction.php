<?php

namespace app\modules\api\common\actions\game;

use app\models\GameUser;
use Yii;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\game
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
            return $this->controller->onError(Yii::t('app', 'user is not in game'));
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