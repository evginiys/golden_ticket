<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\GameUser;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class GetStatisticsAction
 * @package app\modules\api\common\actions\dashboard
 */
class GetStatisticsAction extends Action
{
    /**
     * @return array
     */

    public function run()
    {
        try {
            $userId = Yii::$app->user->id;
            $gamesOfUser = GameUser::numberOfGamesPerUser($userId);
            $winGamesOfUser = GameUser::numberOfWinGamesPerUser($userId);
            return $this->controller->onSuccess(['allGames' => $gamesOfUser, 'winGames' => $winGamesOfUser]);
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
        }
    }
}