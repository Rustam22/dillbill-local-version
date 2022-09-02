<?php

use yii\helpers\Html;
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tutorId',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'date',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'startsAt',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'endsAt',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tutorName',
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

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'level',
        'value' => 'level',
        'filter' => Html::dropDownList(
            'ConversationSearch[level]',
            'select',
            [
                '' => ' ',
                ' ' => 'all',
                'beginner' => 'beginner',
                'elementary' => 'elementary',
                'pre-intermediate' => 'pre-intermediate',
                'intermediate' => 'intermediate',
                'upper-intermediate' => 'upper-intermediate',
                'advanced' => 'advanced',
            ]
        )
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'visible',
    ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete',
            'data-confirm'=>false, 'data-method'=>false,// for override yii data api
            'data-request-method'=>'post',
            'data-toggle'=>'tooltip',
            'data-confirm-title'=>'Are you sure?',
            'data-confirm-message'=>'Are you sure want to delete this item'],
    ],

];