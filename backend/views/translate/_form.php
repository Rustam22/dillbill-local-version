<?php

use marqu3s\summernote\Summernote;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Translate */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="translate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'keyword')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'az')->widget(Summernote::className(), ['clientOptions' => ['height' => '100']]); ?>

    <?= $form->field($model, 'en')->widget(Summernote::className(), ['clientOptions' => ['height' => '100']]); ?>

    <?= $form->field($model, 'ru')->widget(Summernote::className(), ['clientOptions' => ['height' => '100']]); ?>

    <?= $form->field($model, 'tr')->widget(Summernote::className(), ['clientOptions' => ['height' => '100']]); ?>

    <?= $form->field($model, 'pt')->widget(Summernote::className(), ['clientOptions' => ['height' => '100']]); ?>

	<?php if (!Yii::$app->request->isAjax) { ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
