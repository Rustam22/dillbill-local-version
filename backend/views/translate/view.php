<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Translate */
?>
<div class="translate-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'keyword',
            'az:html',
            'en:html',
            'ru:html',
            'tr:html',
            'pt:html',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
