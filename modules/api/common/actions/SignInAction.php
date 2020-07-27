<?php

namespace app\modules\api\common\actions;

use app\models\LoginForm;
use Yii;
use yii\rest\Action;

/**
 * Class SignInAction
 *
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/sign-in",
 *     tags={"Authentication"},
 *     summary="Signs in a user using provided credentials.",
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
 *         format="password",
 *         required=true
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Token response",
 *         @SWG\Schema(ref="#/definitions/TokenResponse")
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Validation failed",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
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

        return $this->controller->onError($loginForm->getErrors(), 400);
    }
}