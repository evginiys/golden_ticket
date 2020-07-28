<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class ChangeNameAction
 * @package app\modules\api\common\actions
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
                return $this->controller->onError(Yii::t('app', $user->getErrors()), 400);
            }
            return $this->controller->onSuccess(['user' => $user->username]);

    }
}