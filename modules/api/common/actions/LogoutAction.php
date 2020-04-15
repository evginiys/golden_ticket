<?php

namespace app\modules\api\common\actions;

use app\models\User;
use yii\rest\Action;

/**
 * Class LogoutAction
 *
 * @package app\modules\api\common\actions
 */
class LogoutAction extends Action
{
    public function run()
    {
        $auth = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);

        if (!is_array($auth))
        {
            return ['status' => false];
        }

        $token = $auth[1];

        $user = User::findOne(['token' => $token]);

        if ($user) {
            $user->token = '';
            $user->save(false);
            return ['status' => true];
        }

        return ['status' => false];
    }
}