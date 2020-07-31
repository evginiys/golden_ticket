<?php

namespace app\modules\api\common\actions;

use app\models\User;
use Yii;
use yii\rest\Action;

/**
 * Class ForgotPasswordAction
 *
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/user/forgot-password",
 *     tags={"Authentication"},
 *     summary="Sends the reset token to the account email address.",
 *     @SWG\Parameter(
 *         in="formData",
 *         name="email",
 *         type="string",
 *         required=true,
 *         description="The email address associated with the account"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="The email is successfully sent",
 *         @SWG\Schema(ref="#/definitions/SuccessSimpleResponse")
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Can't send an email",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="User not found",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class ForgotPasswordAction extends Action
{
    public function run()
    {
        $email = Yii::$app->request->post('email');

        if (!$user = User::findOne(['email' => $email])) {
            return $this->controller->onError(Yii::t('app', 'User not found'), 404);
        }

        $resetToken = Yii::$app->security->generateRandomString(32);

        $user->reset_password_token = $resetToken;
        $user->save(false);

        $mailer = Yii::$app->mailer->compose('reset_password', ['token' => $resetToken])
            ->setTo($email)
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setSubject('Reset Password');

        if (!$mailer->send()) {
            return $this->controller->onError(Yii::t('app', "Can't send an email"), 400);
        }

        return $this->controller->onSuccess(true);
    }
}