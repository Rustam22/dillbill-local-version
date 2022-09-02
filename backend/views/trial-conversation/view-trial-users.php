<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TrialConversationUsers */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trial Conversation Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="trial-conversation-users-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update-trial-users', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete-trial-users', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'trialConversationId',
            'conversationLevel',
            'conversationDate',
            'startsAT',
            'conversationTopic',
            'tutorName',
            'tutorImage',
            'userName',
            'userEmail:email',
            'action',
            'userId',
            'requestDate',
            'requestTime',
        ],
    ]) ?>

</div>
