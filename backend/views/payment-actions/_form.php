<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PaymentActions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-actions-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'userId')->textInput() ?>

    <?= $form->field($model, 'userName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'packetId')->textInput() ?>

    <?= $form->field($model, 'packetName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'planId')->textInput() ?>

    <?= $form->field($model, 'planName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scheduleId')->textInput() ?>

    <?= $form->field($model, 'scheduleName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'priceId')->textInput() ?>

    <?= $form->field($model, 'priceName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pricePeriod')->textInput() ?>

    <?= $form->field($model, 'priceDiscount')->textInput() ?>

    <?= $form->field($model, 'priceTotal')->textInput() ?>

    <?= $form->field($model, 'paidAmount')->textInput() ?>

    <?= $form->field($model, 'promoCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'promoType')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'promoDiscount')->textInput() ?>

    <?= $form->field($model, 'paymentType')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dateTime')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reference')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'reimbursement')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'currency')->textInput() ?>

    <?= $form->field($model, 'paymentDescription')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'timestamp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'xid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rrn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'approval')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rc')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
