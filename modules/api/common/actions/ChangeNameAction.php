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

        if (!$user || !$username = Yii::$app->request->post('username')) {
            return $this->controller->onError(Yii::t('app', 'Cannot change username'));
        } else {
            $user->username = $username;
            if (!$user->save()) {
                return $this->controller->onError(Yii::t('app', $user->getErrors()));
            }
            return $this->controller->onSuccess(['user' => $user->username]);
        }
    }
}