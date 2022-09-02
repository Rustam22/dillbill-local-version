<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PromoActions */
?>
<div class="promo-actions-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'givenByUser',
            'givenByEmail:email',
            'givenByID',
            'takenByEmail:email',
            'takenByUser',
            'givenByPercent',
            'takenByPercent',
            'takenByID',
            'condition',
            'description',
            'date',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
