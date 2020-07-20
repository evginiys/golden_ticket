<?php

namespace app\modules\api\common\actions;

use Yii;
use yii\rest\Action;

/**
 * Class MyAction
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

        if (!$user || !$username = Yii::$app->request->post('username')) {
            return $this->controller->onError(Yii::t('app', 'Cannot change username'), 404);
        } else {
            $user->username = $username;
            $user->update();
            return $this->controller->onSuccess(['user' => $user->username]);
        }


    }
}