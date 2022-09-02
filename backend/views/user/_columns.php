<?php

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

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'color',
        'format' => 'html',
        'value' => function($model) {
            return '<div class="colored-user" style="background-color: '.$model->userProfile->color.';"></div>';
        },
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'username',
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'email',
        'format' => 'email'
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'confirmed',
        'value' => 'userParameters.confirmed'
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'source',
        'value' => 'userProfile.source'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'aim',
        'value' => 'userProfile.aim'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'preliminaryLevel',
        'value' => 'userProfile.preliminaryLevel'
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'availability',
        'value' => 'userParameters.availability'
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=> 'timezone',
        'value' => 'userProfile.timezone'
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'currentLevel',
        'value' => 'userParameters.currentLevel'
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'currentSchedule',
        'value' => 'userParameters.currentSchedule'
    ],


    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'cp',
        'value' => 'userParameters.cp'
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'cpBalance',
        'value' => function($model) {
            return $model->getCpBalance();
        },
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'lpd',
        'value' => 'userParameters.lpd'
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'created_at',
        'value' => function($model) {
            return date('d-m-Y H:i', $model->created_at);
        },
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