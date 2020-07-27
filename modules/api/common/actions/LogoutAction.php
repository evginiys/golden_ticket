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
 *     tags={"Authentication"},
 *     summary="Logs out a user.",
 *     @SWG\Parameter(
 *         in="header",
 *         name="Authorization",
 *         description="Bearer authentication header. The value must have the following format: `Bearer TOKEN`<br/>where `TOKEN` is the authentication token.",
 *         type="string",
 *         required=true,
 *         default="Bearer TOKEN"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Success response",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(
 *                 title="Logout status",
 *                 description="true if logout is successfull",
 *                 property="status",
 *                 type="boolean"
 *             )
 *         )
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @SWG\Schema(ref="#/definitions/UnauthorizedResponse")
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