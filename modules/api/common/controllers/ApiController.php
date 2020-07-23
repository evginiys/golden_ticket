<?php

namespace app\modules\api\common\controllers;

use app\modules\api\common\actions\ErrorAction;
use app\modules\api\common\components\CustomCors;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Class ApiController
 * @package app\modules\api\common\controllers
 */
class ApiController extends ActiveController
{
    /** @var array */
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
            'corsFilter' => [
                'class' => CustomCors::class,
                'cors' => [
                    'Origin' => [
                        '*'
                    ],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Headers' => [
                        'origin',
                        'authorization',
                        'content-type'
                    ]
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
     * @return array
     */
    public function onSuccess($data)
    {
        return $this->respond(false, $data);
    }

    /**
     * @param $error
     * @param $data
     *
     * @param int $code
     * @return array
     */
    protected function respond($error, $data, $code = 200)
    {
        Yii::$app->response->setStatusCode($code);
        return [
            'error' => (int)$error,
            'data' => $data
        ];
    }

    /**
     * @param $message
     *
     * @param int $code
     * @return array
     */
    public function onError($message, $code = 200)
    {
        Yii::error(var_export($message, 1));
        return $this->respond(true, $message, $code);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
                'modelClass' => ''
            ]
        ];
    }
}