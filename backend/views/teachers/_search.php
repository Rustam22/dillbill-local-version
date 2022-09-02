<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TeachersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="teachers-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'teacherName') ?>

    <?= $form->field($model, 'teacherZoom') ?>

    <?= $form->field($model, 'image') ?>

    <?= $form->field($model, 'presentation') ?>

    <?php // echo $form->field($model, 'landing') ?>

    <?php // echo $form->field($model, 'orderNumber') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'experience') ?>

    <?php // echo $form->field($model, 'description_az') ?>

    <?php // echo $form->field($model, 'description_en') ?>

    <?php // echo $form->field($model, 'description_ru') ?>

    <?php // echo $form->field($model, 'description_tr') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
