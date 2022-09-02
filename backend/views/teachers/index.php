<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TeachersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Teachers';
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->pagination->pageSize = 10;

?>



<div class="teachers-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Teachers', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php $dataProvider->setSort(['defaultOrder' => ['orderNumber' => SORT_DESC]]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'teacherName',
            [
                'attribute' => 'image',
                'value'     => 'image',
                'format'    => ['image', ['width' => '80', 'height' => '80']],
            ],
            [
                'attribute' => 'teacherZoom',
                'format'    => ['html'],
                'value' => function ($model) {
                    return '<a href="'.$model->teacherZoom.'">zoom</a>';
                },
            ],
            [
                'attribute' => 'presentation',
                'format'    => ['html'],
                'value' => function ($model) {
                    return '<a href="'.$model->presentation.'">youtube</a>';
                },
            ],

            'landing',
            'orderNumber',
            //'country',
            'experience',
            //'description_az',
            //'description_en',
            //'description_ru',
            //'description_tr',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>



<br><br>
<div class="review-index">
    <h1>Review</h1>

    <p><?= Html::a('Create Review', ['create-review'], ['class' => 'btn btn-success']) ?></p>
    <?php $reviewDataProvider->setSort(['defaultOrder' => ['orderNumber' => SORT_DESC]]); ?>

    <?= GridView::widget([
        'dataProvider' => $reviewDataProvider,
        'filterModel' => $reviewSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],


            //'id',
            'name',
            [
                'attribute' => 'image',
                'value'     => 'image',
                'format'    => ['image', ['width' => '80', 'height' => '80']],
            ],
            'stars',
            'orderNumber',
            'beforeLevel',
            'afterLevel',
            'language',
            'description',
            'position',
            //'name'

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',

                'buttons' => [
                    'view' => function ($url, $model) {
                        $url = Url::to(['teachers/view-review', 'id' => $model->id], true);
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                    },

                    'update' => function ($url, $model) {
                        $url = Url::to(['teachers/update-review', 'id' => $model->id], true);
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                    },

                    'delete' => function ($url, $model) {
                        $url = Url::to(['teachers/delete-review', 'id' => $model->id], true);
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url);
                    },
                ]
            ]

        ],
    ]); ?>
</div>
