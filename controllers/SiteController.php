<?php

namespace app\controllers;

use app\models\SignUpForm;
use Exception;
use yii\db\Exception as DbException;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

/**
 * Class SiteController
 *
 * @package app\controllers
 *
 * @SWG\Swagger(
 *     basePath="/api/v1",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="Golden Ticket API"),
 *     @SWG\Parameter(
 *         parameter="authorization",
 *         in="header",
 *         name="Authorization",
 *         description="Bearer authentication header. The value must have the following format: `Bearer TOKEN`<br/>where `TOKEN` is the authentication token.",
 *         type="string",
 *         required=true,
 *         default="Bearer TOKEN"
 *     ),
 *     @SWG\Response(
 *         response="unauthorized",
 *         description="Unauthorized",
 *         @SWG\Schema(ref="#/definitions/UnauthorizedResponse")
 *     ),
 *     @SWG\Response(
 *         response="success_simple",
 *         description="Success",
 *         @SWG\Schema(ref="#/definitions/SuccessSimpleResponse")
 *     )
 * )
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'docs' => [
                'class' => 'yii2mod\swagger\SwaggerUIRenderer',
                'restUrl' => Url::to(['site/json-schema']),
            ],
            'json-schema' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                'scanDir' => [
                    Yii::getAlias('@app/controllers'),
                    Yii::getAlias('@app/models'),
                    Yii::getAlias('@app/modules/api'),
                ],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Sign Up action.
     *
     * @return mixed
     * @throws Exception
     */
    public function actionSignUp()
    {
        $model = new SignUpForm();
        if ($model->load(Yii::$app->request->post())) {
            try {
                if ($model->signup()) {
                    return $this->goHome();
                }
            } catch (Exception $e) {
                if ($e instanceof DbException) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Database error'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', $e->getMessage()));
                }
            }
        }

        return $this->render('sign-up', [
            'model' => $model
        ]);
    }
}
