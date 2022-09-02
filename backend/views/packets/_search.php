<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PacketsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="packets-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'period') ?>

    <?= $form->field($model, 'lesson') ?>

    <?= $form->field($model, 'nameKeyword') ?>

    <?= $form->field($model, 'descriptionKeyword') ?>

    <?php // echo $form->field($model, 'usd') ?>

    <?php // echo $form->field($model, 'azn') ?>

    <?php // echo $form->field($model, 'try') ?>

    <?php // echo $form->field($model, 'brl') ?>

    <?php // echo $form->field($model, 'discountPercent') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
