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
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'givenByUser',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'takenByUser',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'givenByEmail',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'takenByEmail',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'givenByPercent',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'takenByPercent',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'condition',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'description',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'date',
    ],
    //[
    //    'class'=>'\kartik\grid\DataColumn',
    //    'attribute'=>'givenByID',
  //  ],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'takenByID',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'date',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
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
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'], 
    ],

];   