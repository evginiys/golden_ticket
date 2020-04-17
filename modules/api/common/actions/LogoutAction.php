<?php

namespace app\modules\api\common\actions;

use Yii;
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
        $user = Yii::$app->user->identity;

        if (!$user) {
            return ['status' => false];
        }

        $user->token = '';
        $user->save(false);

        return ['status' => Yii::$app->user->logout()];
    }
}