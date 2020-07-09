<?php

namespace app\modules\api\common\controllers;

use app\models\Ticket;
use app\models\TicketPack;
use app\modules\api\common\actions\ticket\BuyTickets;
use app\modules\api\common\actions\ticket\GetPacksAction;
use app\modules\api\common\actions\ticket\GetTicketsAction;

/**
 * Class TicketController
 * @package app\modules\api\common\controllers
 */
class TicketController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = Ticket::class;

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'packs' => [
                'class' => GetPacksAction::class,
                'modelClass' => TicketPack::class
            ],
            'tickets' => [
                'class' => GetTicketsAction::class,
                'modelClass' => Ticket::class
            ],
            'buy' => [
                'class' => BuyTickets::class,
                'modelClass' => Ticket::class
            ],
        ];
    }
}