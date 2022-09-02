<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ConversationUsers */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Conversation Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="conversation-users-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'conversationId',
            'conversationLevel',
            'tutorName',
            [
                'attribute' => 'tutorImage',
                'format' => ['image', ['width' => '70','height' => '70']],
            ],
            'userName',
            'userEmail',
            'action',
            'conversationDate',
            'startsAT',
            'requestDate',
            'requestTime',
            'userId',
        ],
    ]) ?>

</div>
