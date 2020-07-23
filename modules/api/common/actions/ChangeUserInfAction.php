<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class changeUserInfAction
 * @package app\modules\api\common\actions
 */
class ChangeUserInfAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $user = Yii::$app->user->identity;
        $phone = Yii::$app->request->post('phone');
        $email = Yii::$app->request->post('email');
        $username = Yii::$app->request->post('username');

        if (isset($phone)) {
            $user->phone = $phone;
        }
        if (isset($email)) {
            $user->email = $email;
        }
        if (isset($username)) {
            $user->username = $username;
        }

        if (!$user->save()) {
            return $this->controller->onError($user->getErrors());
        }

        return $this->controller->onSuccess($user->getAttributes(['username', 'phone', 'email']));
    }
}
