<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PaymentActionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-actions-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'userId') ?>

    <?= $form->field($model, 'userName') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'packetId') ?>

    <?php // echo $form->field($model, 'packetName') ?>

    <?php // echo $form->field($model, 'planId') ?>

    <?php // echo $form->field($model, 'planName') ?>

    <?php // echo $form->field($model, 'scheduleId') ?>

    <?php // echo $form->field($model, 'scheduleName') ?>

    <?php // echo $form->field($model, 'priceId') ?>

    <?php // echo $form->field($model, 'priceName') ?>

    <?php // echo $form->field($model, 'pricePeriod') ?>

    <?php // echo $form->field($model, 'priceDiscount') ?>

    <?php // echo $form->field($model, 'priceTotal') ?>

    <?php // echo $form->field($model, 'paidAmount') ?>

    <?php // echo $form->field($model, 'promoCode') ?>

    <?php // echo $form->field($model, 'promoType') ?>

    <?php // echo $form->field($model, 'promoDiscount') ?>

    <?php // echo $form->field($model, 'paymentType') ?>

    <?php // echo $form->field($model, 'dateTime') ?>

    <?php // echo $form->field($model, 'code') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'createdAt') ?>

    <?php // echo $form->field($model, 'updatedAt') ?>

    <?php // echo $form->field($model, 'reference') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'reimbursement') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'paymentDescription') ?>

    <?php // echo $form->field($model, 'timestamp') ?>

    <?php // echo $form->field($model, 'xid') ?>

    <?php // echo $form->field($model, 'rrn') ?>

    <?php // echo $form->field($model, 'approval') ?>

    <?php // echo $form->field($model, 'pan') ?>

    <?php // echo $form->field($model, 'rc') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
