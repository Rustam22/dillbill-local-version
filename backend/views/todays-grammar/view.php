<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TodaysGrammar */
?>
<div class="todays-grammar-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'startDate',
            'level',
            'lessonId',
            'lessonName',
        ],
    ]) ?>

</div>
