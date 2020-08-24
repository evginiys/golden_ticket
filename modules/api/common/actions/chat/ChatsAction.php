<?php

namespace app\modules\api\common\actions\chat;

use app\models\User;
use Yii;
use yii\rest\Action;

/**
 * Class ChatsAction
 * @package app\modules\api\common\actions\dashboard
 */
class ChatsAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $chats = User::findOne(Yii::$app->user->id)->inChats;
        if (!$chats) {
            return $this->controller->onError(Yii::t('app', 'Not chats'), 404);
        }
        return $this->controller->onSuccess($chats);
    }
}