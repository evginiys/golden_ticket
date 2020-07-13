<?php

namespace app\modules\websocket\handlers;

use app\models\OnlineUser;

/**
 * Class OnlineHandler
 * @package app\modules\websocket\handlers
 */
class OnlineHandler extends BaseHandler
{
    /** @var int */
    public $userId;

    /**
     * @return bool
     */
    public function handle()
    {
        return OnlineUser::setOnline($this->userId);
    }
}