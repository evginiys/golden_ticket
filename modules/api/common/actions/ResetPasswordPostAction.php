<?php

namespace app\modules\api\common\actions;

use app\models\User;
use Exception;
use Yii;
use yii\rest\Action;

/**
 * Class ResetPasswordAction
 *
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/reset-password-post",
 *     tags={"Authentication"},
 *     summary="Checks reset token and sets the new password.",
 *     @SWG\Parameter(
 *         in="formData",
 *         name="token",
 *         type="string",
 *         required=true,
 *         description="The reset token from email"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="password",
 *         type="string",
 *         format="password",
 *         description="The new password"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="The new password successfully set",
 *         @SWG\Schema(ref="#/definitions/SuccessSimpleResponse")
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Error: blank password provided or user data saving failed",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="User not found",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class ResetPasswordPostAction extends Action
{
    public function run()
    {
        try {
            $token = Yii::$app->request->post('token');
            $password = Yii::$app->request->post('password');

            $user = User::findOne(['reset_password_token' => $token]);

            if (!$user) {
                return $this->controller->onError(Yii::t('app', 'User not found'), 404);
            }

            if (empty($password)) {
                return $this->controller->onError(Yii::t('app', 'New password cannot be blank.'), 400);
            }

            $user->setPassword($password);
            $user->reset_password_token = null;
            $user->date_reset_password = date('Y-m-d H:i:s');
            $user->save(false);
        } catch (Exception $e) {
            return $this->controller->onError($e->getMessage(), 400);
        }

        return ['success' => true];
    }
}