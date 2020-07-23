<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class LogoutAction
 *
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/logout",
 *     tags={"Auth"},
 *     summary="Logs out a user.",
 *     @SWG\Response(
 *         response=200,
 *         description="Success response"
 *     ),
 * )
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