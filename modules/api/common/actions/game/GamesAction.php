<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\game
 */
class GamesAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $data = Game::find()->where(['status' => 1])->andWhere(['date_start'=>date()])->one();
        return $this->controller->onSuccess($data);
    }
}