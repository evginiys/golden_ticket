<?php

namespace app\modules\api\common\actions\promo;

use app\models\Promo;
use Yii;
use yii\rest\Action;

/**
 * Class GetPromoAction
 * @package app\modules\api\common\actions\promo
 */
class GetPromoAction extends Action
{
    /**
     * @return mixed
     */
    public function run()
    {
        $promo = Promo::find()->select(['id', 'name', 'description', 'cost', 'imageUrl', 'expiration_at', 'created_at'])->all();
        if ($promo) {
            return $this->controller->onSuccess($promo);
        } else {
            return $this->controller->onError(Yii::t('app', 'Not found promo'), 404);
        }
    }
}