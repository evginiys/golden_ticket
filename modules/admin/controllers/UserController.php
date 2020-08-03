<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\NotFoundHttpException;
use Exception;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AdminController
{
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password);
            $model->generateApiToken();

            if ($model->save()) {
                $playerRole = Yii::$app->authManager->getRole(User::ROLE_PLAYER);
                Yii::$app->authManager->assign($playerRole, $model->id);

                $model->updateTokenExpirationDate();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->password = '';

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        Yii::$app->authManager->revokeAll($model->id);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Bans an user.
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionBan($id) {
        $user = $this->findModel($id);

        if (!Yii::$app->authManager->getAssignment(User::ROLE_BANNED, $user->id)) {
            $bannedRole = Yii::$app->authManager->getRole(User::ROLE_BANNED);
            Yii::$app->authManager->assign($bannedRole, $user->id);

            Yii::$app->session->setFlash('success', Yii::t('app', 'Banned successfully'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'User is banned already'));
        }

        return $this->redirect(['user/index']);
    }

    /**
     * Removes ban of an user.
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUnban($id) {
        $user = $this->findModel($id);
        $bannedRole = Yii::$app->authManager->getRole(User::ROLE_BANNED);

        if (Yii::$app->authManager->revoke($bannedRole, $user->id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Ban removed successfully'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'User is not banned'));
        }

        return $this->redirect(['user/index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
