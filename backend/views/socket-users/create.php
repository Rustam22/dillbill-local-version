<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SocketUsers */

$this->title = 'Create Socket Users';
$this->params['breadcrumbs'][] = ['label' => 'Socket Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="socket-users-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
