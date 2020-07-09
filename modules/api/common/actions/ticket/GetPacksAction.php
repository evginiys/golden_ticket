<?php

namespace app\modules\api\common\actions\ticket;

use app\models\TicketPack;
use yii\rest\Action;

/**
 * Class GetPacksAction
 * @package app\modules\api\common\actions\ticket
 */
class GetPacksAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $data = TicketPack::find()->all();

        return $this->controller->onSuccess($data);
    }
}