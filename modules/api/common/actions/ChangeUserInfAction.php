<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class changeUserInfAction
 * @package app\modules\api\common\actions
 */
class changeUserInfAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $user = Yii::$app->user->identity;
        $phone = Yii::$app->request->post('phone');
        if (isset($phone)) {
            $user->phone = $phone;
        }
        if (!empty($email = Yii::$app->request->post('email'))) {
            $user->email = $email;
        }
        if (!empty($username = Yii::$app->request->post('username'))) {
            $user->username = $username;
        }
        if (!$user->save(true)) {
            return $this->controller->onError(Yii::t('app', $user->getErrors()));
        }

        return $this->controller->onSuccess($user->getAttributes(['username', 'phone', 'email']));
    }
}
