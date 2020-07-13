<?php

namespace app\modules\api\common\actions;

use app\models\User;
use Yii;
use yii\rest\Action;

/**
 * Class MyAction
 * @package app\modules\api\common\actions
 */
class MyAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $user = User::findOne(Yii::$app->request->get('id', 0));

        if (!$user) {
            return $this->controller->onError(Yii::t('app', 'User is not found'), 404);
        }

        return $this->controller->onSuccess($user);
    }
}