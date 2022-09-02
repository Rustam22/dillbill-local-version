<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TodaysGrammar */
/* @var $form yii\widgets\ActiveForm */


?>

<style>
    .lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
    }
    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 64px;
        height: 64px;
        margin: 5% auto;
        border-radius: 50%;
        border: 6px solid #337ab7;
        animation: lds-dual-ring 1.2s linear infinite;
    }
    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    .display-none {
        display: none;
    }
</style>

<script>
    $(document).ready(function() {
        let _csrf_frontend = '<?= Yii::$app->request->csrfToken ?>'
        let fillLessons = '<?= Url::to(['todays-grammar/lessons'], true) ?>'

        function loadLessons(level) {
            $.ajax({
                url : fillLessons,
                type : 'POST',
                data : {'_csrf-frontend': _csrf_frontend, 'level': level},
                beforeSend: function() {
                    $('.lds-dual-ring').removeClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)
                    let innerSelect = '<option value="">Select Lesson ...</option>'

                    for(let i = 0; i < data.lessonsByLevel.length; i++) {
                        innerSelect += '<option value="' + data.lessonsByLevel[i].id + '">' + data.lessonsByLevel[i].description + '</option>'
                    }

                    $('#lessons-id').html(innerSelect)
                },
                error : function(request, error) {
                    console.log('error')
                },
                complete: function() {
                    $('.lds-dual-ring').addClass('display-none')
                }
            });
        }

        $('#level-id').on('change', function() {
            if(this.value.length > 2 ) {
                loadLessons(this.value)
            }
        });

        $('#lessons-id').on('change', function() {
            if(this.value.length > 0 ) {
                console.log(this.value + ' - ' + $('#lessons-id option[value="' + this.value + '"]').html())
                $('input[name="TodaysGrammar[lessonName]"]').val($('#lessons-id option[value="' + this.value + '"]').html())
                $('input[name="TodaysGrammar[lessonId]"]').val(this.value)
            }
        });

        if($('#level-id').val().length > 2) {
            loadLessons($('#level-id').val())
        }
    })
</script>


<div class="todays-grammar-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'startDate')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select issue date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true
        ],
    ])
    ?>

    <?php

        $levelList = [
            'beginner' => 'Beginner',
            'elementary' => 'Elementary',
            'pre-intermediate' => 'Pre-intermediate',
            'intermediate' => 'Intermediate',
            'upper-intermediate' => 'Upper-intermediate',
            'advanced' => 'Advanced'
        ];

        echo $form->field($model, 'level')->dropDownList($levelList, ['id' => 'level-id', 'prompt' => 'Select Level ...']);

    ?>


    <div class="row justify-content-around">
        <div class="col-5" align="center">
            <div class="lds-dual-ring display-none"></div>
        </div>
    </div>


    <div class="form-group field-level-id required">
        <label class="control-label" for="lessons-id">Lessons</label>
        <select id="lessons-id" class="form-control" aria-required="true">
            <!-- Parse Place -->
        </select>
        <div class="help-block"></div>
    </div>


    <?= $form->field($model, 'lessonName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lessonId')->textInput() ?>


  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
