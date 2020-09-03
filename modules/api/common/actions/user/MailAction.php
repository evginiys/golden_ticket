<?php

namespace app\modules\api\common\actions\user;

use app\models\MailForm;
use Yii;
use yii\rest\Action;

/**
 * Class MailAction
 *
 * @package app\modules\api\common\actions\user
 *
 * @SWG\Post(path="/user/mail",
 *     tags={"User"},
 *     summary="Sends an email message to the specified email address.",
 *     @SWG\Parameter(ref="#/parameters/authorization"),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="message",
 *         type="string",
 *         required=true,
 *         description="Text of the message"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="email",
 *         type="string",
 *         required=true,
 *         description="Email address of the recipient"
 *     ),
 *     @SWG\Parameter(
 *         in="formData",
 *         name="subject",
 *         type="string",
 *         required=true,
 *         description="Subject of the message"
 *     ),
 *     @SWG\Response(response=200, ref="#/responses/success_simple"),
 *     @SWG\Response(
 *         response=400,
 *         description="Email validation failed",
 *         @SWG\Schema(ref="#/definitions/ErrorResponse")
 *     ),
 *     @SWG\Response(response=401, ref="#/responses/unauthorized"),
 * )
 */
class MailAction extends Action
{
    /**
     * @return array
     */
    public function run()
    {
        $model = new MailForm();
        if ($model->load(Yii::$app->request->post(), "") && $model->validate()) {
            Yii::$app->mailer->compose()
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject($model->subject)
                ->setHtmlBody(Yii::t('app', 'Message from: ') . $model->email . "<br>" . $model->message)
                ->setTextBody(Yii::t('app', 'Message from: ') . $model->email . PHP_EOL . $model->message)
                ->send();
            return $this->controller->onSuccess(true);
        } else {
            return $this->controller->onError($model->errors, 400);
        }
    }
}