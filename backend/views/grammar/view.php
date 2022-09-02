<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Grammar */
?>
<div class="grammar-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'description',
            'url:url',
            'level',
            'type',
            'orderNumber',
            'active',
        ],
    ]) ?>

</div>
