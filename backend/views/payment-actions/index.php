<?php

use backend\models\ConversationUsers;
use backend\models\PaymentActions;
use backend\models\User;
use common\models\UserParameters;
use common\models\UserProfile;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PaymentActionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Actions';
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->pagination->pageSize = 100;
$dataProvider->setSort(['defaultOrder' => ['dateTime' => SORT_DESC]]);

?>

<?php

$fromDate = (Yii::$app->getRequest()->getQueryParam('fromDay') != null) ? Yii::$app->getRequest()->getQueryParam('fromDay') : '2021-12-27';

?>

<div class="container" style="width: 100%;display: none;">

    <div class="row">
        <div class="col-12">
            <form action="/backend/web/" method="get" role="form">
                <input type="hidden" name="r" value="payment-actions">
                <label>
                    <input type="text" name="fromDay" placeholder="from date <?= $fromDate ?>" style="min-width: 165px;">
                </label>
                <button type="submit">OK</button> <button type="button" class="btn-success" id="see-statistics">See Statistics</button><br>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <br>
            <b>Total trial users: <code>43</code></b><br>
        </div>
    </div>
</div>



<style>
    .container {
        width: 95%;
        margin: auto;
    }
</style>
<div class="payment-actions-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Payment Actions', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'userId',
            [
                'attribute' => 'userName',
                'value' => function($model) {
                    return $model->userName;
                },
            ],
            'email:email',
            [
                'attribute' => 'source',
                'value' => function($model) {
                    try {
                        return UserProfile::findOne(['userId' => $model->userId])->source;
                    } catch (Exception $exception) {
                        return null;
                    }
                },
            ],
            [
                'attribute' => 'aim',
                'value' => function($model) {
                    try {
                        return UserProfile::findOne(['userId' => $model->userId])->aim;
                    } catch (Exception $exception) {
                        return null;
                    }
                },
            ],
            [
                'attribute' => 'Current Level',
                'value' => function($model) {
                    try {
                        return UserParameters::findOne(['userId' => $model->userId])->currentLevel;
                    } catch (Exception $exception) {
                        return null;
                    }
                },
            ],
            //'packetId',
            'packetName',
            //'planId',
            //'planName',
            //'scheduleId',
            //'scheduleName',
            //'priceId',
            //'priceName',
            //'pricePeriod',
            //'priceDiscount',
            //'priceTotal',
            [
                'attribute' => 'paidAmount',
                'value' => function($model) {
                    return '😛 '.$model->paidAmount;
                },
            ],
            //'promoType',
            //'promoDiscount',
            //'paymentType',
            [
                'attribute' => 'dateTime',
                'value' => function($model) {
                    return (new DateTime($model->dateTime))->format('d-m-Y ⏱H:i');
                },
            ],
            'promoCode',
            //'code',
            //'description',
            //'reference',
            //'amount',
            //'reimbursement',
            //'currency',
            //'paymentDescription',
            //'timestamp',
            //'xid',
            //'rrn',
            //'approval',
            //'pan',
            //'rc',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
