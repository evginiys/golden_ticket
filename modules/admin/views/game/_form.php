<?php

use app\models\Game;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Game */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="game-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList(Game::getTypeDescription()) ?>

    <?= $form->field($model, 'date_start')->widget(DateTimePicker::class, [
        'convertFormat' => true,
        'pluginOptions' => [
            'format' => 'php:Y-m-d H:i:s',
            'startDate' => date('Y-m-d H:i:s'),
            'todayHighlight' => true
        ]
    ]) ?>

    <?= $form->field($model, 'cost')->input('number') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
