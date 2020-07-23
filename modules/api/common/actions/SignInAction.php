<?php

namespace app\modules\api\common\actions;

use Yii;
use app\models\LoginForm;
use yii\rest\Action;

/**
 * Class SignInAction
 *
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/sign-in",
 *     tags={"Auth"},
 *     summary="Signs in a user by provided credentials.",
 *     @SWG\Parameter(
 *         in="formData",
 *         name="username",
 *         type="string",
 *         required=true
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="password",
 *         type="string",
 *         required=true
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Token response"
 *     ),
 * )
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
            $loginForm->getUser()->generateApiToken();
            $loginForm->getUser()->updateTokenExpirationDate();

            return $this->controller->onSuccess(['token' => $loginForm->getUser()->token]);
        }

        return $this->controller->onError($loginForm->getErrors());
    }
}