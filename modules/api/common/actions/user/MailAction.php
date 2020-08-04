<?php

namespace app\modules\api\common\actions\user;

use app\models\MailForm;
use Yii;
use yii\rest\Action;

/**
 * Class MailAction
 * @package app\modules\api\common\actions\user
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
            return $this->controller->onError($model->errors);
        }
    }
}