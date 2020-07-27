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
 * @SWG\Get(path="/reset-password-get",
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
 *         description="Success response",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(
 *                 title="Reset token search status",
 *                 description="`true` if user with provided reset token exists",
 *                 property="success",
 *                 type="boolean"
 *             )
 *         )
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

        return ['success' => true];
    }
}