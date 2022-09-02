<?php

use yii\helpers\Html;
use yii\bootstrap4\Modal;
use kartik\grid\GridView;
use hoaaah\ajaxcrud\CrudAsset; 
use hoaaah\ajaxcrud\BulkButtonWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TrialConversationSearch */
/* @var $trialUsersModelSearch backend\models\TrialConversationUsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $trialUsersDataProvider yii\data\ActiveDataProvider */

$this->title = 'Trial Conversations';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

$dataProvider->pagination->pageSize = 5;
$dataProvider->setSort(['defaultOrder' => ['date' => SORT_DESC]]);

?>

<script>
    document.styleSheets[0].disabled = true;
</script>
<style>
    #p0 {
        width: fit-content;
        width: -moz-fit-content;
    }
    .container {
        width: 80%;
    }
    select {
        font-size: 20px;
    }
</style>

<div class="conversation-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                        ['role'=>'modal-remote','title'=> 'Create new Conversations','class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                        ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                    '{toggleData}'.
                    '{export}'
                ],
            ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i> &nbsp;&nbsp;Conversations',
                //'before'=>'<em>* Resize table columns just like a spreadsheet by dragging the column edges.</em>',
                'after'=>BulkButtonWidget::widget([
                        'buttons'=>Html::a('<i class="glyphicon glyphicon-trash"></i>&nbsp; Delete All',
                            ["bulkdelete"] ,
                            [
                                "class"=>"btn btn-danger btn-xs",
                                'role'=>'modal-remote-bulk',
                                'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                                'data-request-method'=>'post',
                                'data-confirm-title'=>'Are you sure?',
                                'data-confirm-message'=>'Are you sure want to delete this item'
                            ]),
                    ]).
                    '<div class="clearfix"></div>',
            ]
        ])?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>



<br><br>
<style>
    select {
        font-size: 20px;
    }
    .container {
        width: 90%;
    }
</style>
<div class="trial-conversation-users-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <!--<p><?/*= Html::a('create user', ['create-trial-users'], ['class' => 'btn btn-success']) */?></p>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php $trialUsersDataProvider->setSort(['defaultOrder' => ['requestDate' => SORT_DESC]]); ?>


    <?= GridView::widget([
        'dataProvider' => $trialUsersDataProvider,
        'filterModel' => $trialUsersModelSearch,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'conversationId',
            [
                'attribute' => 'conversationLevel',
                'value' => 'conversationLevel',
                'filter' => Html::dropDownList(
                    'TrialConversationUsersSearch[conversationLevel]',
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
                    'TrialConversationUsersSearch[action]',
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

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',

                'buttons' => [
                    'view' => function ($url, $model) {
                        $url = Url::to(['trial-conversation/view-trial-users', 'id' => $model->id], true);
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                    },

                    'update' => function ($url, $model) {
                        $url = Url::to(['trial-conversation/update-trial-users', 'id' => $model->id], true);
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                    },

                    'delete' => function ($url, $model) {
                        $url = Url::to(['trial-conversation/delete-trial-users', 'id' => $model->id], true);
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url);
                    },
                ]
            ]

        ],
    ]); ?>


</div>
