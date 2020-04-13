<?php

namespace app\modules\api\common\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Class ApiController
 *
 * @package app\modules\api\common
 */
class ApiController extends ActiveController
{
    public $notNeedTokenActions = [];

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    HttpBearerAuth::class,
                ],
                'except' => $this->notNeedTokenActions
            ],
        ];
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function onSuccess($data)
    {
        return $this->respond(false, $data);
    }

    /**
     * @param $message
     *
     * @return array
     */
    public function onError($message)
    {
        Yii::error(var_export($message, 1));
        return $this->respond(true, $message);
    }

    /**
     * @param $error
     * @param $data
     *
     * @return array
     */
    protected function respond($error, $data)
    {
        return [
            'error' => (int)$error,
            'data' => $data
        ];
    }
}