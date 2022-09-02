<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TrialConversationUsers */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="trial-conversation-users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'trialConversationId')->textInput() ?>

    <?= $form->field($model, 'conversationLevel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'conversationDate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'startsAT')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'conversationTopic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tutorName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tutorImage')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'userName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'userEmail')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action')->dropDownList([ 'reserve' => 'Reserve', 'enter' => 'Enter', 'cancel' => 'Cancel', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'userId')->textInput() ?>

    <?= $form->field($model, 'requestDate')->textInput() ?>

    <?= $form->field($model, 'requestTime')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
