<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConversationUsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conversation Users';
$this->params['breadcrumbs'][] = $this->title;



?>
<style>
    select {
        font-size: 20px;
    }
    .container {
        width: 90%;
    }
</style>
<div class="conversation-users-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Conversation Users', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
        $dataProvider->setSort(['defaultOrder' => ['requestDate' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = 100;
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'conversationId',
            [
                'attribute' => 'conversationLevel',
                'value' => 'conversationLevel',
                'filter' => Html::dropDownList(
                        'ConversationUsersSearch[conversationLevel]',
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
            'tutorName',
            [
                'attribute' => 'tutorImage',
                'value' => 'tutorImage',
                'format' => ['image', ['width' => '70','height' => '70']],
            ],
            'userName',
            'userEmail',
            [
                'attribute' => 'action',
                'value' => 'action',
                'filter' => Html::dropDownList(
                    'ConversationUsersSearch[action]',
                    'select',
                    [
                        '' => ' ',
                        ' ' => 'all',
                        'reserve' => 'Reserve',
                        'enter' => 'Enter Room',
                    ]
                )
            ],
            'conversationDate',
            'startsAT',
            'requestDate',
            'requestTime',
            //'userId',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
