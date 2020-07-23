<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use yii\rest\Action;

/**
 * Class GamesAction
 * @package app\modules\api\common\actions\game
 */
class GamesAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $data = Game::find()->where(['status' => 0])->orWhere(['status' => 1])->all();
        return $this->controller->onSuccess($data);
    }
}