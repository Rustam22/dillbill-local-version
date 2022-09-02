<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Packets */

$this->title = 'Create Packets';
$this->params['breadcrumbs'][] = ['label' => 'Packets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="packets-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
