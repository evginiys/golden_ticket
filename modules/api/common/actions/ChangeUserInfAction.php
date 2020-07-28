<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class ChangeUserInfAction
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/user/change-user-inf",
 *     tags={"User"},
 *     summary="Changes information about the current user.",
 *     @SWG\Parameter(
 *         in="header",
 *         name="Authorization",
 *         description="Bearer authentication header. The value must have the following format: `Bearer TOKEN`<br/>where `TOKEN` is the authentication token.",
 *         type="string",
 *         required=true,
 *         default="Bearer TOKEN"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="username",
 *         type="string",
 *         description="The new user name (optional)"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="phone",
 *         type="string",
 *         description="The new user phone number (optional)"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="email",
 *         type="string",
 *         description="The new user email address (optional)"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="New information about the user",
 *         @SWG\Schema(ref="#/definitions/User")
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Validation of the new user fields failed",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @SWG\Schema(ref="#/definitions/UnauthorizedResponse")
 *     )
 * )
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
            return $this->controller->onError($user->getErrors(), 400);
        }

        return $this->controller->onSuccess($user->getAttributes(['username', 'phone', 'email']));
    }
}
