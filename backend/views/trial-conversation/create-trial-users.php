<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TrialConversationUsers */

$this->title = 'Create Trial Conversation Users';
$this->params['breadcrumbs'][] = ['label' => 'Trial Conversation Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trial-conversation-users-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-trial-users', [
        'model' => $model,
    ]) ?>

</div>
