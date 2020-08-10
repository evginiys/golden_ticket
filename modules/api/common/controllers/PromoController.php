<?php

namespace app\modules\api\common\controllers;

use app\models\Promo;
use app\modules\api\common\actions\promo\GetPromoAction;
use app\modules\api\common\actions\promo\BuyPromoAction;

/**
 * Class PromoController
 * @package app\modules\api\common\controllers
 */
class PromoController extends ApiController
{
    /**
     * @var string
     */
    public $modelClass = Promo::class;

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'get-promo' => [
                'class' => GetPromoAction::class,
                'modelClass' => Promo::class
            ],
            'buy-promo' => [
                'class' => BuyPromoAction::class,
                'modelClass' => Promo::class
            ],
        ];
    }
}