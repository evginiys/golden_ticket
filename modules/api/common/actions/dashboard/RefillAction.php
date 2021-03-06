<?php

namespace app\modules\api\common\actions\dashboard;

use app\models\Payment;
use Exception;
use Yii;
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
 *     @SWG\Response(response=200, ref="#/responses/success_simple"),
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
        $amount = Yii::$app->request->post('amount');

        if (is_numeric($amount) && $amount > 0) {
            try {
                if (!Payment::refill(Yii::$app->user->id, $amount)) {
                    return $this->controller->onError(Yii::t('app', 'Cannot refill wallet'), 400);
                }

                return $this->controller->onSuccess(true);
            } catch (Exception $e) {
                return $this->controller->onError(Yii::t('app', $e->getMessage()), 400);
            }
        }

        return $this->controller->onError(Yii::t('app', 'Amount must be a number greater than 0'), 400);
    }
}