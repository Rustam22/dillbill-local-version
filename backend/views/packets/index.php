<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PacketsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Packets';
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
<div class="packets-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Packets', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nameKeyword',
            'descriptionKeyword',
            'period',
            'usd',
            'azn',
            'try',
            'brl',
            //'lesson',
            'discountPercent',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
