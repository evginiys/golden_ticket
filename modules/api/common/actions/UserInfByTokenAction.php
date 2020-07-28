<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class MyAction
 * @package app\modules\api\common\actions
 *
 * @SWG\Get(path="/user/user-inf-by-token",
 *     tags={"User"},
 *     summary="Retrieves the information about current user.",
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
 *         description="Information about the user",
 *         @SWG\Schema(ref="#/definitions/User")
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @SWG\Schema(ref="#/definitions/UnauthorizedResponse")
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="User is not found",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
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
            return $this->controller->onError(Yii::t('app', 'User is not found'), 404);
        }

        return $this->controller->onSuccess([
            'username' => $user->username,
            'phone' => $user->phone,
            'email' => $user->email
        ]);
    }
}