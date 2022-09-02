<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PromoActions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="promo-actions-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'givenByUser')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'givenByEmail')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'givenByID')->textInput() ?>

    <?= $form->field($model, 'takenByEmail')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'takenByUser')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'givenByPercent')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'takenByPercent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'takenByID')->textInput() ?>

    <?= $form->field($model, 'condition')->dropDownList([ 'used' => 'Used', 'unused' => 'Unused', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>


    <?php $model->date = ($model->date == null) ? date('Y-m-d') : $model->date; ?>
    <?= $form->field($model, 'date')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select issue date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true
        ],
    ])
    ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
