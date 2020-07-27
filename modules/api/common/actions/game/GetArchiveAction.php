<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use Yii;
use yii\rest\Action;

/**
 * Class GetArchiveAction
 * @package app\modules\api\common\actions\game
 */
class GetArchiveAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $gameId = Yii::$app->request->get('gameId');
        $game = Game::find()->where(['id' => $gameId])->one();
        if ($game) {
            $archive = $game->getArchiveUrl();
            return $this->controller->onSuccess(['archive' => $archive]);
        } else {
            return $this->controller->onError(Yii::t('app', "Game isn`t found"));
        }
    }
}