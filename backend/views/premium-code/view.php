<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PremiumCode */
?>
<div class="premium-code-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'packetId',
            'discount',
            'nTime:datetime',
            'used',
            'type',
            'active',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
