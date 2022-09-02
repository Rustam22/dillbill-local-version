<?php

use backend\models\Packets;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PremiumCode */
/* @var $form yii\widgets\ActiveForm */

//debug($packets);

?>

<div class="premium-code-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group field-premiumcode-packetid required">
        <label class="control-label" for="premiumcode-packetid">Packet</label>
        <?php $packets = Packets::find()->select(['nameKeyword', 'id'])->asArray()->all();?>
        <select id="premiumcode-packetid" class="form-control" name="PremiumCode[packetId]" aria-required="true">
            <?php if($model->packetId != null) { $packet = Packets::findOne(['id' => $model->packetId]); ?>
                <option value="<?= ($packet == null) ? '' : $packet->id; ?>" selected> <?= ($packet == null) ? '' : $packet->nameKeyword; ?> </option>
            <?php } else { ?>
                <option value="">Select Packet ...</option>
            <?php } ?>
            <?php foreach ($packets as $key => $value) { ?>
                <option value="<?= $value['id'] ?>">
                    <?= $value['nameKeyword'] ?>
                </option>
            <?php } ?>
        </select>
        <div class="help-block"></div>
    </div>

    <?= $form->field($model, 'discount')->textInput() ?>

    <?= $form->field($model, 'nTime')->textInput() ?>

    <?php if($model->used == null) { ?>
        <?= $form->field($model, 'used')->textInput(['value' => 0]) ?>
    <?php } else { ?>
        <?= $form->field($model, 'used')->textInput() ?>
    <?php } ?>

    <?= $form->field($model, 'type')->dropDownList([ 'premium' => 'Premium', 'coupon' => 'Coupon'], ['prompt' => '', 'value' => 'premium']) ?>

    <?= $form->field($model, 'active')->dropDownList([ 'yes' => 'Yes', 'no' => 'No'], ['prompt' => '', 'value' => 'yes']) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
