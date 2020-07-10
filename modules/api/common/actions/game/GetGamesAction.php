<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\ticket
 */
class GetGamesAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $data = Game::find()->where('status',1)->all();

        return $this->controller->onSuccess($data);
    }
}