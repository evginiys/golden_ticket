<?php

namespace app\modules\admin\controllers;

use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class AdminController
 *
 * @package app\modules\admin\controllers
 */
class AdminController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => false,
                        'roles' => [User::ROLE_BANNED]
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }
}