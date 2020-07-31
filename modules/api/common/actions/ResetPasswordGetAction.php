<?php

namespace app\modules\api\common\actions;

use app\models\User;
use Yii;
use yii\rest\Action;

/**
 * Class ResetPasswordAction
 *
 * @package app\modules\api\common\actions
 *
 * @SWG\Get(path="/user/reset-password-get",
 *     tags={"Authentication"},
 *     summary="Checks if the user with provided reset token exists, that is password change is possible.",
 *     @SWG\Parameter(
 *         in="query",
 *         name="token",
 *         type="string",
 *         required=true,
 *         description="The reset token from email"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Reset token exists, password change is possible",
 *         @SWG\Schema(ref="#/definitions/SuccessSimpleResponse")
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Token is not found",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class ResetPasswordGetAction extends Action
{
    public function run()
    {
        $token = Yii::$app->request->get('token');
        $user = User::findOne(['reset_password_token' => $token]);

        if (!$user) {
            return $this->controller->onError(Yii::t('app', 'Token is not found'), 404);
        }

        return $this->controller->onSuccess(true);
    }
}