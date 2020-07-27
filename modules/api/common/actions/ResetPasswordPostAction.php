<?php

namespace app\modules\api\common\actions;

use app\models\User;
use Yii;
use Exception;
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
 *         description="Success response",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(
 *                 title="Reset token sending status",
 *                 description="`true` if an email is sent successfully",
 *                 property="success",
 *                 type="boolean"
 *             )
 *         )
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
                return $this->controller->onError('User not found');
            }

            if (empty($password)) {
                return $this->controller->onError('New password cannot be blank.');
            }

            $user->setPassword($password);
            $user->reset_password_token = null;
            $user->date_reset_password = date('Y-m-d H:i:s');
            $user->save(false);
        } catch (Exception $e) {
            return $this->controller->onError($e->getMessage());
        }

        return ['success' => true];
    }
}