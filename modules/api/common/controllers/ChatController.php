<?php

namespace app\modules\api\common\controllers;

use app\models\Chat;
use app\models\ChatUser;
use app\modules\api\common\actions\chat\ChatsAction;

/**
 * Class ChatController
 * @package app\modules\api\common\controllers
 */
class ChatController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = Chat::class;

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'chats' => [
                'class' => ChatsAction::class,
                'modelClass' => ChatUser::class
            ],
        ];
    }
}