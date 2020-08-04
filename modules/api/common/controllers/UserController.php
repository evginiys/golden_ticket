<?php

namespace app\modules\api\common\controllers;

use app\models\User;
use app\modules\api\common\actions\user\ChangeNameAction;
use app\modules\api\common\actions\ForgotPasswordAction;
use app\modules\api\common\actions\LogoutAction;
use app\modules\api\common\actions\user\MailAction;
use app\modules\api\common\actions\user\MyAction;
use app\modules\api\common\actions\ResetPasswordGetAction;
use app\modules\api\common\actions\ResetPasswordPostAction;
use app\modules\api\common\actions\SignInAction;
use app\modules\api\common\actions\SignUpAction;
use app\modules\api\common\actions\user\UserInfByTokenAction;
use app\modules\api\common\actions\user\ChangeUserInfAction;

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
        'sign-up', 'sign-in', 'forgot-password', 'reset-password-get', 'reset-password-post'
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
            'forgot-password' => [
                'class' => ForgotPasswordAction::class,
                'modelClass' => $this->modelClass
            ],
            'reset-password-get' => [
                'class' => ResetPasswordGetAction::class,
                'modelClass' => $this->modelClass
            ],
            'reset-password-post' => [
                'class' => ResetPasswordPostAction::class,
                'modelClass' => $this->modelClass
            ],
            'my' => [
                'class' => MyAction::class,
                'modelClass' => $this->modelClass
            ],
            'user-inf-by-token' => [
                'class' => UserInfByTokenAction::class,
                'modelClass' => $this->modelClass
            ],
            'change-name' => [
                'class' => ChangeNameAction::class,
                'modelClass' => $this->modelClass
            ],
            'change-user-inf' => [
                'class' => ChangeUserInfAction::class,
                'modelClass' => $this->modelClass
            ],
            'mail' => [
                'class' => MailAction::class,
                'modelClass' => $this->modelClass
            ],
        ];
    }
}