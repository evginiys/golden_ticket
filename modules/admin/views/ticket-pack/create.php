<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TicketPack */

$this->title = Yii::t('app', 'Create Ticket Pack');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Packs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-pack-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
