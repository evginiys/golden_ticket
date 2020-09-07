<?php

use app\models\GameUser;
use app\models\Payment;
use app\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'username',
            'email:email',
            'phone',
            [
                'value' => function (User $model) {
                    $roles = Yii::$app->authManager->getRolesByUser($model->id);
                    $roleNames = [];
                    foreach ($roles as $role) {
                        $roleNames[] = $role->description;
                    }
                    return implode(',<br>', $roleNames);
                },
                'label' => Yii::t('app', 'Roles'),
                'format' => 'html'
            ],
            [
                'value' => function (User $model) {
                    return $model->getBalance(Payment::CURRENCY_RUR);
                },
                'label' => Yii::t('app', 'RUR')
            ],
            [
                'value' => function (User $model) {
                    return $model->getBalance(Payment::CURRENCY_COIN);
                },
                'label' => Yii::t('app', 'Coins')
            ],
            [
                'value' => function (User $model) {
                    return $model->getBalance(Payment::CURRENCY_COUPON);
                },
                'label' => Yii::t('app', 'Coupons')
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}&nbsp{update}&nbsp{ban}&nbsp{delete}',
                'buttons' => [
                    'ban' => function ($item, User $model, $key) {
                        if (!Yii::$app->authManager->getAssignment(User::ROLE_BANNED, $model->id)) {
                            return Html::a('<span class="glyphicon glyphicon-ban-circle"></span>',
                                ['ban', 'id' => $model->id],
                                [
                                    'title' => Yii::t('app', 'Ban'),
                                    'aria-label' => Yii::t('app', 'Ban'),
                                    'data-pjax' => 0
                                ]
                            );
                        } else {
                            return Html::a('<span class="glyphicon glyphicon-ok"></span>',
                                ['unban', 'id' => $model->id],
                                [
                                    'title' => Yii::t('app', 'Remove ban'),
                                    'aria-label' => Yii::t('app', 'Remove ban'),
                                    'data-pjax' => 0
                                ]
                            );
                        }
                    }
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
