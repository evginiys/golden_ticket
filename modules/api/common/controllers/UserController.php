<?php

namespace app\modules\api\common\controllers;

use app\models\User;
use app\modules\api\common\actions\LogoutAction;
use app\modules\api\common\actions\SignInAction;
use app\modules\api\common\actions\SignUpAction;

/**
 * Class UserController
 *
 * @package app\modules\api\common
 */
class UserController extends ApiController
{
    /**
     * @var array
     */
    public $notNeedTokenActions = [
        'sign-up', 'sign-in'
    ];

    /**
     * @var string
     */
    public $modelClass = User::class;

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'sign-up' => [
                'class' => SignUpAction::class,
                'modelClass' => $this->modelClass
            ],
            'sign-in' => [
                'class' => SignInAction::class,
                'modelClass' => $this->modelClass
            ],
            'logout' => [
                'class' => LogoutAction::class,
                'modelClass' => $this->modelClass
            ],
        ];
    }
}