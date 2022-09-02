<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PaymentActions */

$this->title = 'Create Payment Actions';
$this->params['breadcrumbs'][] = ['label' => 'Payment Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-actions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
