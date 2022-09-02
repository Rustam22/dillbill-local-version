<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TrialConversationUsers */

$this->title = 'Update Trial Conversation Users: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trial Conversation Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

?>

<div class="trial-conversation-users-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-trial-users', [
        'model' => $model,
    ]) ?>

</div>
