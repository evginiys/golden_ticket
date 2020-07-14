<?php

namespace app\modules\api\common\actions;

use app\models\OnlineUser;
use yii\rest\Action;

/**
 * Class OnlineCounterAction
 * @package app\modules\api\common\actions
 */
class OnlineCounterAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        return $this->controller->onSuccess(['count' => OnlineUser::getOnlineUsersCount()]);
    }
}