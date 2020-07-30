<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use Yii;
use yii\db\Exception;
use yii\rest\Action;

/**
 * Class RefillAction
 * @package app\modules\api\common\actions\dashboard
 *
 * @SWG\Post(path="/dashboard/refill",
 *     tags={"Dashboard"},
 *     summary="Refills the RUR wallet of current user.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="amount",
 *         type="number",
 *         required=true,
 *         description="Amount of RUR to refill"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Refill successful",
 *         @SWG\Schema(
 *             type="object",
 *             @SWG\Property(property="error", type="integer", example=0),
 *             @SWG\Property(property="data", type="boolean", example=true)
 *         )
 *     ),
 *     @SWG\Response(
 *         response=400,
 *         description="Cannot refill wallet or incorrect amount value is provided",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized")
 * )
 */
class RefillAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        try {
            if ($amount = Yii::$app->request->post('amount')) {
                if (Payment::refill(Yii::$app->user->id, $amount)) {
                    return $this->controller->onSuccess(true);
                } else {
                    throw new Exception("Cannot refill wallet");
                }
            } else {
                throw new Exception( "Cannot refill wallet");
            }
        } catch (Exception $e) {
            return $this->controller->onError(Yii::t('app',$e->getMessage()), 400);
        }
    }
}