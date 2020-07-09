<?php

namespace app\modules\api\common\actions\ticket;

use app\models\Ticket;
use yii\rest\Action;

/**
 * Class GetTicketsAction
 * @package app\modules\api\common\actions\ticket
 */
class GetTicketsAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $data = Ticket::find()->joinWith('ticketPack')->all();

        return $this->controller->onSuccess($data);
    }
}