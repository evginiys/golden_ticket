<?php

namespace app\modules\api\common\actions\user;

use app\models\Chat;
use Exception;
use Yii;
use yii\helpers\Json;
use yii\rest\Action;

/**
 * Class ChangeUserInfAction
 * @package app\modules\api\common\actions
 *
 * @SWG\Post(path="/user/change-user-inf",
 *     tags={"User"},
 *     summary="Changes information about current user.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
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
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(property="data", ref="#/definitions/User")
 *         )
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Validation of the new user fields failed",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
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
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (isset($phone)) {
                $user->phone = $phone;
            }
            if (isset($email)) {
                $user->email = $email;
            }
            if (isset($username)) {

                $oldUserName = $user->username;
                $user->username = $username;
                $chats = $user->getInChats()->where(["type" => Chat::TYPE_PRIVATE])->all();
                if ($chats) {
                    foreach ($chats as $chat) {
                        $nameChat = Json::decode($chat->name);
                        $index = array_search($oldUserName, $nameChat);
                        $nameSecondUser = $nameChat[$oldUserName];
                        unset($nameChat[$oldUserName]);
                        if (!$index) {
                            throw new Exception("Not found username in chat");
                        }
                        $nameChat[$user->username] = $nameSecondUser;
                        $nameChat[$index] = $user->username;
                        $chat->name = Json::encode($nameChat);
                        if (!$chat->save()) {
                            throw new Exception($chat->getErrors());
                        }
                    }
                }

            }
            if (!$user->save()) {
                throw new Exception($user->getErrors());
            }
            $transaction->commit();
            return $this->controller->onSuccess($user->getAttributes(['username', 'phone', 'email']));
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->controller->onError(Yii::t('app', $e->getMessage()));
        }
    }
}
