<?php

use app\models\GameUser;
use app\models\Payment;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$roles = Yii::$app->authManager->getRolesByUser($model->id);
$roleNames = [];
foreach ($roles as $role) {
    $roleNames[] = $role->description;
}
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            'phone',
            [
                'value' => implode(', ', $roleNames),
                'label' => Yii::t('app', 'Roles')
            ],
            'token',
            'date_token_expired',
        ],
    ]) ?>

    <h2><?= Yii::t('app', 'Balance') ?></h2>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'value' => $model->getBalance(Payment::CURRENCY_RUR),
                'label' => Yii::t('app', 'RUR')
            ],
            [
                'value' => $model->getBalance(Payment::CURRENCY_COIN),
                'label' => Yii::t('app', 'Coins')
            ],
            [
                'value' => $model->getBalance(Payment::CURRENCY_COUPON),
                'label' => Yii::t('app', 'Coupons')
            ],
        ]
    ]) ?>

</div>
