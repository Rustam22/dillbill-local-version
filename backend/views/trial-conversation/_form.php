<?php

use kartik\date\DatePicker;
use richardfan\widget\JSRegister;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TrialConversation */
/* @var $form yii\widgets\ActiveForm */

$teachers = \backend\models\Teachers::find()->select(['id', 'teacherName'])->asArray()->all();

//debug($teachers);

?>

<style>
    .modal-dialog {
        width: 900px !important;
    }
    input[name="TrialConversation[endsAt]"] {

    }
</style>

<?php JSRegister::begin(); ?>
<script>
    $(document).ready(function() {
        $('#teacher-select').on('change', function() {
            $('input[name="TrialConversation[tutorId]"]').val('')

            if(this.value.length > 0) {
                $('input[name="TrialConversation[tutorId]"]').val($(this).find(':selected').data('id'))
            }
        })
    })
</script>
<?php JSRegister::end(); ?>

<div class="conversation-form">

    <?php $form = ActiveForm::begin(); ?>



    <?php $model->date = ($model->date == null) ? date('Y-m-d') : $model->date; ?>
    <?= $form->field($model, 'date')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select issue date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true
        ],
    ])
    ?>


    <?= $form->field($model, 'startsAt')->textInput(['type' => 'time']) ?>
    <?= $form->field($model, 'endsAt')->textInput(['type' => 'time']) ?>



    <?php JSRegister::begin(); ?>
    <script>
        $(document).ready(function () {
            $(document).on('click', '#trialconversation-endsat', function() {
                let startsAt = $('#trialconversation-startsat').val()
                if(startsAt.length === 5) {
                    let conversationStarts = new Date()
                    conversationStarts.setHours(startsAt.substring(0, 2))
                    conversationStarts.setMinutes(startsAt.substring(3, 5))

                    let conversationEnds = new Date()
                    conversationEnds.setHours(conversationStarts.getHours() + 1)
                    conversationEnds.setMinutes(conversationStarts.getMinutes())

                    $('#trialconversation-endsat').val(conversationEnds.getHours() + ':' + conversationEnds.getMinutes())
                }
            })
        })
    </script>
    <?php JSRegister::end(); ?>



    <div class="form-group field-teacher required">
        <label class="control-label" for="teacher-select">Teachers</label>
        <select id="teacher-select" class="form-control" name="teacher-select" aria-required="true">
            <option value="" data-name="" data-image="" data-zoom="" data-presentation="">Select Teacher ...</option>
            <?php foreach ($teachers as $key => $value) { ?>
                <option data-id="<?= $value['id'] ?>">
                    <?= $value['teacherName'] ?>
                </option>
            <?php } ?>
        </select>
        <div class="help-block"></div>
    </div>


    <?= $form->field($model, 'tutorId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'level')->dropDownList([ 'beginner' => 'Beginner', 'elementary' => 'Elementary', 'pre-intermediate' => 'Pre-intermediate', 'intermediate' => 'Intermediate', 'upper-intermediate' => 'Upper-intermediate', 'advanced' => 'Advanced'], ['prompt' => '']) ?>

    <?= $form->field($model, 'createdAt')->textInput(['maxlength' => true, 'value' => date('Y-m-d H:i:s')])->label(false) ?>

    <?php $model->visible = ($model->visible == null) ? 'yes' : $model->visible ?>
    <?= $form->field($model, 'visible')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>


    <?php ActiveForm::end(); ?>

</div>
