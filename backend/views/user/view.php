<?php

use backend\models\Packet;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

?>
<div class="user-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',

            //'username',

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'name',
                'value' => function($model) {
                    return $model->userProfile->name;
                },
            ],

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'surname',
                'value' => function($model) {
                    return $model->userProfile->surname;
                },
            ],

            //'password_hash',
            //'password_reset_token',
            //'verification_token',

            'email:email',

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'phone',
                'value' => function($model) {
                    return $model->userProfile->phone;
                },
            ],

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'confirmed',
                'value' => function($model) {
                    return $model->userParameters->confirmed;
                },
            ],

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'availability',
                'value' => function($model) {
                    return $model->userParameters->availability;
                },
            ],

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'availabilityLCD',
                'value' => function($model) {
                    return $model->userParameters->availabilityLCD;
                },
            ],

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'startDate',
                'value' => function($model) {
                    return $model->userParameters->startDate;
                },
            ],

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'proficiency',
                'value' => function($model) {
                    return $model->userParameters->proficiency;
                },
            ],

            //'levelUpTestDate',
            //'verificationCode',
            //'auth_key',
            //'status',
            //'created_at',
            //'updated_at',
            //'mobile',
            //'birthday',

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'currentLevel',
                'value' => function($model) {
                    return $model->userParameters->currentLevel;
                },
            ],

            //'currentPacketId',

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'currentSchedule',
                'value' => function($model) {
                    return $model->userParameters->currentSchedule;
                },
            ],

            //'promoCode',

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'userTimeZone',
                'value' => function($model) {
                    return $model->userProfile->timezone;
                },
            ],

            //'condition',

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'cp',
                'value' => function($model) {
                    return $model->userParameters->cp;
                },
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
                'value' => function($model) {
                    return $model->userParameters->lpd;
                },
            ],

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'googleCalendar',
                'value' => function($model) {
                    return $model->userParameters->googleCalendar;
                },
            ],

            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'calendarGmail',
                'value' => function($model) {
                    return $model->userParameters->calendarGmail;
                },
            ],
        ],
    ]) ?>

</div>
