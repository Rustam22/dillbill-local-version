<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\DeveloperSettings */

$this->title = 'Update Developer Settings: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Developer Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="developer-settings-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
