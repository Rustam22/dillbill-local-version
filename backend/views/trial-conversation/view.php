<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TrialConversation */
?>
<style>
    .modal-dialog {
        width: 900px !important;
    }
</style>
<div class="conversation-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'date',
            'startsAt',
            'endsAt',
            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute' => 'tutorEmail',
                'format' => 'email',
                'value' => function($model) {
                    if($model->tutorEmail == null) {
                        return $model->tutor->email;
                    }

                    return $model->tutorEmail;
                },
            ],
            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute' => 'tutorName',
                'value' => function($model) {
                    if($model->tutorName == null) {
                        return $model->tutor->teacherName;
                    }

                    return $model->tutorName;
                },
            ],
            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute' => 'tutorImage',
                'format' => ['image', ['width' => '80']],
                'value' => function($model) {
                    if($model->tutorImage == null) {
                        return $model->tutor->image;
                    }

                    return $model->tutorImage;
                },
            ],

            'level',

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute' => 'zoom',
                'format' => ['url'],
                'value' => function($model) {
                    try {
                        return $model->tutor->teacherZoom;
                    } catch (Exception $message) {
                        return null;
                    }
                },
            ],
            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute' => 'videoPresentation',
                'format' => ['url'],
                'value' => function($model) {
                    try {
                        return $model->tutor->presentation;
                    } catch (Exception $message) {
                        return null;
                    }
                },
            ],

            'createdAt',
            'visible'
        ],
    ]) ?>

</div>
