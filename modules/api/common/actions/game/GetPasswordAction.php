<?php

namespace app\modules\api\common\actions\game;

use app\models\Game;
use Yii;
use yii\rest\Action;

/**
 * Class GetPasswordAction
 * @package app\modules\api\common\actions\game
 */
class GetPasswordAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $gameId = Yii::$app->request->get('gameId');
        $game = Game::find()->where(['id' => $gameId])->andWhere(['status' => Game::STATUS_ENDED])->one();
        if ($game) {
            $password = $game->getArchivePassword();
            return $this->controller->onSuccess(['password' => $password]);
        } else {
            return $this->controller->onError(Yii::t('app', "Game isn`t found"));
        }
    }
}