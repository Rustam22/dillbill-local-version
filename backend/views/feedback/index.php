<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Feedbacks';
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->pagination->pageSize = 100;
$dataProvider->setSort(['defaultOrder' => ['created_at' => SORT_DESC]]);


?>

<style>
    .container {
        width: 95%;
        margin: auto;
    }
</style>

<div class="feedback-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Feedback', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute'=> 'tutor',
                'value' => function($model) {
                    return $model->class->teacher->teacherName;
                },
            ],
            [
                'attribute'=> 'image',
                'format' => ['image', ['width' => '80']],
                'value' => function($model) {
                    return $model->class->teacher->image;
                },
            ],
            [
                'attribute'=> 'classDate',
                'value' => function($model) {
                    return $model->class->date;
                },
            ],
            [
                'attribute'=> 'startsAt',
                'value' => function($model) {
                    return $model->class->startsAt;
                },
            ],
            [
                'attribute'=> 'level',
                'value' => function($model) {
                    return $model->class->level;
                },
            ],
            [
                'attribute'=> 'username',
                'value' => function($model) {
                    return $model->user->username;
                },
            ],
            [
                'attribute'=> 'email',
                'format' => 'email',
                'value' => function($model) {
                    return $model->user->email;
                },
            ],
            //'userId',
            //'classId',
            'topic',
            'score',
            'comment:html',
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return date('d-m-Y H:i', $model->created_at);
                },
            ],
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
