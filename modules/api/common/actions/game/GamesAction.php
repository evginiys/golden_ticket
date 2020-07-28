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
        $data = Game::find()->where(['status' => [Game::STATUS_SCHEDULED, Game::STATUS_IN_PROCESS]])
            ->select(['id', 'type', 'cost', 'date_start', 'date_end'])->all();
        return $this->controller->onSuccess($data);
    }
}