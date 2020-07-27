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
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(
 *                 title="Error status",
 *                 description="0 when User is successfully created, 1 otherwise",
 *                 property="error",
 *                 type="integer"
 *             ),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(
 *                     property="token",
 *                     type="string"
 *                 )
 *             )
 *         )
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

        return $this->controller->onError($loginForm->getErrors());
    }
}