<?php

namespace app\modules\api\common\actions\game;

use app\models\OnlineUser;
use yii\rest\Action;

/**
 * Class OnlineCountAction
 * @package app\modules\api\common\actions
 */
class OnlineCountAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        return $this->controller->onSuccess(['count' => OnlineUser::getOnlineUsersCount()]);
    }
}