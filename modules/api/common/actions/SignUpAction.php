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
            $user = new User([
                'username' => Yii::$app->request->post('username'),
                'email' => Yii::$app->request->post('email'),
                'phone' => Yii::$app->request->post('phone'),
                'password' => Yii::$app->request->post('password'),
            ]);

            $user->generateApiToken();

            if ($user->validate()) {
                $user->setPassword($user->password);

                if ($user->save(false)) {
                    $user->updateTokenExpirationDate();

                    $playerRole = Yii::$app->authManager->getRole(User::ROLE_PLAYER);
                    Yii::$app->authManager->assign($playerRole, $user->id);

                    return $this->controller->onSuccess(['token' => $user->token]);
                }
            }

            return $this->controller->onError($user->getErrors());
        } catch (Exception $e) {
            return $this->controller->onError($e->getMessage());
        }
    }
}