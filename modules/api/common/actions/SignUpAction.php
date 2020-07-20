<?php

namespace app\modules\api\common\actions;

use app\models\User;
use Yii;
use yii\rest\Action;
use Exception;

/**
 * Class SignUpAction
 *
 * @package app\modules\api\common\actions
 */
class SignUpAction extends Action
{
    public function run()
    {
        try {
            $user = new User();
            $user->username = Yii::$app->request->post('username');
            $user->email = Yii::$app->request->post('email');
            $user->phone = Yii::$app->request->post('phone');
            $user->setPassword(Yii::$app->request->post('password'));
            $user->generateApiToken();

            if (!$user->save()) {
                return $this->controller->onError($user->getErrors());
            }

            $user->updateTokenExpirationDate();

            $playerRole = Yii::$app->authManager->getRole(User::ROLE_PLAYER);
            Yii::$app->authManager->assign($playerRole, $user->id);

            return $this->controller->onSuccess(['token' => $user->token]);
        } catch (Exception $e) {
            return $this->controller->onError($e->getMessage());
        }
    }
}