<?php

namespace app\modules\api\common\actions\user;

use Yii;
use yii\rest\Action;

/**
 * Class ChangeNameAction
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/user/change-name",
 *     tags={"User"},
 *     summary="Sets the new name for current user.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="The new user name",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(property="user", type="string", example="John Doe")
 *             )
 *         )
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Validation of the new user name failed",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class ChangeNameAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $user = Yii::$app->user->identity;
        $username = Yii::$app->request->post('username');
            $user->username = $username;
            if (!$user->save()) {
                return $this->controller->onError($user->getErrors(), 400);
            }
            return $this->controller->onSuccess(['user' => $user->username]);

    }
}