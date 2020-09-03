<?php

namespace app\modules\api\common\actions\promo;

use app\models\Promo;
use Yii;
use yii\rest\Action;

/**
 * Class GetPromoAction
 *
 * @package app\modules\api\common\actions\promo
 *
 * @SWG\Get(path="/promo/get-promo",
 *     tags={"Promo"},
 *     summary="Retrieves a list of promos.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Response(
 *         response=200,
 *         description="List of promos",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(
 *                 property="data",
 *                 type="array",
 *                 @SWG\Items(ref="#/definitions/Promo")
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 *     @SWG\Response(
 *         response=404,
 *         description="Not found promo",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     )
 * )
 */
class GetPromoAction extends Action
{
    /**
     * @return mixed
     */
    public function run()
    {
        $promo = Promo::find()->select([
            'id',
            'name',
            'description',
            'cost',
            'imageUrl',
            'expiration_at',
            'created_at'
        ])->all();

        if ($promo) {
            return $this->controller->onSuccess($promo);
        } else {
            return $this->controller->onError(Yii::t('app', 'Not found promo'), 404);
        }
    }
}