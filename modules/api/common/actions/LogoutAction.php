<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class LogoutAction
 *
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/user/logout",
 *     tags={"Authentication"},
 *     summary="Logs out a user.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="Logout status",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(
 *                 property="status",
 *                 type="boolean",
 *                 example=true
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 *     )
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