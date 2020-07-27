<?php

namespace app\modules\api\common\actions;

use app\models\User;
use Yii;
use yii\rest\Action;

/**
 * Class ResetPasswordAction
 *
 * @package app\modules\api\common\actions
 */
class ResetPasswordGetAction extends Action
{
    public function run()
    {
        $token = Yii::$app->request->get('token');
        $user = User::findOne(['reset_password_token' => $token]);

        if (!$user) {
            return $this->controller->onError('Token is not found',404);
        }

        return ['success' => true];
    }
}