<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Teachers */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Teachers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="teachers-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'email',
            'teacherName',
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
                    return '<a href="'.$model->teacherZoom.'">youtube</a>';
                },
            ],
            'image:image',
            'landing',
            'orderNumber',
            'country',
            'experience',
            'description_az',
            'description_en',
            'description_ru',
            'description_tr',
            'description_pt',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
