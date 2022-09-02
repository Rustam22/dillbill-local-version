<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model->userProfile, 'name') ?>

    <?= $form->field($model->userProfile, 'surname') ?>

    <?php // echo $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'verification_token') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php echo $form->field($model->userParameters, 'confirmed') ?>

    <?php // echo $form->field($model, 'proficiency') ?>

    <?php // echo $form->field($model, 'levelUpTestDate') ?>

    <?php // echo $form->field($model, 'verificationCode') ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'birthday') ?>

    <?php // echo $form->field($model, 'currentLevel') ?>

    <?php // echo $form->field($model, 'currentPacketId') ?>

    <?php // echo $form->field($model, 'currentSchedule') ?>

    <?php // echo $form->field($model, 'promoCode') ?>

    <?php // echo $form->field($model, 'condition') ?>

    <?php // echo $form->field($model, 'userTimeZone') ?>

    <?php // echo $form->field($model, 'cp') ?>

    <?php  echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'lpd') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
