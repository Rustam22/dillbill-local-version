<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Packets */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="packets-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'period')->textInput() ?>

    <?= $form->field($model, 'lesson')->textInput() ?>

    <?= $form->field($model, 'nameKeyword')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descriptionKeyword')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'usd')->textInput() ?>

    <?= $form->field($model, 'azn')->textInput() ?>

    <?= $form->field($model, 'try')->textInput() ?>

    <?= $form->field($model, 'brl')->textInput() ?>

    <?= $form->field($model, 'discountPercent')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
