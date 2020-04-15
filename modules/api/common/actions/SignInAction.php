<?php

namespace app\modules\api\common\actions;

use Yii;
use app\models\LoginForm;
use yii\rest\Action;

/**
 * Class SignInAction
 *
 * @package app\modules\api\common\actions
 */
class SignInAction extends Action
{
    public function run()
    {
        $loginForm = new LoginForm([
            'username' => Yii::$app->request->post('username'),
            'password' => Yii::$app->request->post('password'),
            'rememberMe' => false,
        ]);

        if ($loginForm->login()) {
            $loginForm->getUser()->updateTokenExpirationDate();

            return $this->controller->onSuccess(['token' => $loginForm->getUser()->token]);
        }

        return $this->controller->onError($loginForm->getErrors());
    }
}