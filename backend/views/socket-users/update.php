<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SocketUsers */

$this->title = 'Update Socket Users: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Socket Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="socket-users-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
