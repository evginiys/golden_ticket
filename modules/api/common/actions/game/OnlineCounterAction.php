<?php

namespace app\modules\api\common\actions\game;

use app\models\OnlineUser;
use yii\rest\Action;

/**
 * Class OnlineCounterAction
 * @package app\modules\api\common\actions
 *
 * @SWG\Get(path="/game/online-count",
 *     tags={"Game"},
 *     summary="Gets the count of currently online users.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="Count of users online",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="object",
 *                 @SWG\Property(property="count", type="integer", example=100)
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class OnlineCounterAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        return $this->controller->onSuccess(['count' => OnlineUser::getOnlineUsersCount()]);
    }
}