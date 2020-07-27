<?php

namespace app\modules\api\common\actions;

use app\models\User;
use Yii;
use yii\rest\Action;

/**
 * Class ForgotPasswordAction
 *
 * @package app\modules\api\common\actions
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

        return ['success' => true];
    }
}