<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $user backend\models\User */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($user->userProfile, 'color')->textInput(['maxlength' => true]) ?>

    <?= $form->field($user->userProfile, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($user->userProfile, 'surname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($user->userParameters, 'availability')->textInput(['maxlength' => true]) ?>

    <?= $form->field($user->userParameters, 'availabilityLCD')->widget(DatePicker::class, [
            'options' => ['placeholder' => 'Select issue date ...'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true
            ],
        ])
    ?>

    <?= $form->field($user->userParameters, 'currentLevel')->dropDownList([ 'beginner' => 'Beginner', 'elementary' => 'Elementary', 'pre-intermediate' => 'Pre-intermediate', 'intermediate' => 'Intermediate', 'upper-intermediate' => 'Upper-intermediate', 'advanced' => 'Advanced', 'empty' => 'Empty'], ['prompt' => '']) ?>

    <?= $form->field($user->userParameters, 'startDate')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select issue date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true
        ],
    ])
    ?>

    <?= $form->field($user->userParameters, 'confirmed')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($user->userParameters, 'proficiency')->dropDownList(['level' => 'Level', 'start-date' => 'Start date', 'level-start-date' => 'Level and start date', 'no' => 'No'], ['prompt' => '']) ?>

    <?= $form->field($user->userParameters, 'currentSchedule')->textInput(['maxlength' => true]) ?>

    <?= $form->field($user->userProfile, 'timezone')->dropDownList([Yii::$app->devSet->timeZones()], ['prompt' => '']) ?>

    <?= $form->field($user->userParameters, 'cp')->textInput() ?>

    <?= $form->field($user->userParameters, 'cpBalance')->textInput() ?>

    <?= $form->field($user->userParameters, 'lpd')->widget(DatePicker::class, [
            'options' => ['placeholder' => 'Select issue date ...'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true
            ],
        ])
    ?>

    <?= $form->field($user->userParameters, 'googleCalendar')->dropDownList([ 'yes' => 'Yes', 'no' => 'No'], ['prompt' => '']) ?>

    <?= $form->field($user->userParameters, 'calendarGmail')->textInput(['maxlength' => true]) ?>

    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton($user->isNewRecord ? 'Create' : 'Update', ['class' => $user->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
