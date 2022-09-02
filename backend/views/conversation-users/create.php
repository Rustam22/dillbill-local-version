<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ConversationUsers */

$this->title = 'Create Conversation Users';
$this->params['breadcrumbs'][] = ['label' => 'Conversation Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conversation-users-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
