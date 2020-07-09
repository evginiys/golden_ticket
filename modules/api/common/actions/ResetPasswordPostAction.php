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