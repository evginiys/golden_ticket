<?php

namespace app\modules\api\common\actions\user;

use Yii;
use yii\rest\Action;

/**
 * Class MyAction
 * @package app\modules\api\common\actions
 *
 * @SWG\Get(path="/user/user-inf-by-token",
 *     tags={"User"},
 *     summary="Retrieves the information about current user.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="Information about the user",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(property="data", ref="#/definitions/User")
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="User is not found",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class UserInfByTokenAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $user = Yii::$app->user->identity;

        if (!$user) {
            return $this->controller->onError(Yii::t('app', 'User is not found'), 404);
        }

        return $this->controller->onSuccess([
            'username' => $user->username,
            'phone' => $user->phone,
            'email' => $user->email
        ]);
    }
}