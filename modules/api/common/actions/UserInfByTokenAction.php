<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class MyAction
 * @package app\modules\api\common\actions
 */
class UserInfByTokenAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $user = Yii::$app->user->identity;

        if (!$user) {
            return $this->controller->onError(Yii::t('app', 'User is not found'));
        }

        return $this->controller->onSuccess(['username' => $user->username, 'phone' => $user->phone, 'email' => $user->email]);
    }
}