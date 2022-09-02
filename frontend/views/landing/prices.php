<?php

use backend\models\Packets;
use yii\helpers\Url;

$currency = 'usd';
$currencyIcon = 'USD';
$multiplier = 1;
$USD = 1;
$ipCountry = Yii::$app->devSet->ip_info("Visitor", "Country");
$lang = Yii::$app->language;

//$ipCountry = 'Turkey';
//debug($_SERVER["HTTP_CF_IPCOUNTRY"]);
//debug($ipCountry);
//debug($_SERVER["REMOTE_ADDR"]);

if ($ipCountry == 'Azerbaijan') {
    $currency = 'azn';
    $currencyIcon = 'AZN';
} elseif ($ipCountry == 'Turkey') {
    $currency = 'try';
    $currencyIcon = 'TL';
} elseif ($ipCountry == 'Brazil') {
    $currency = 'brl';
    $currencyIcon = 'BRL';
}

$packets = Packets::find()->where(['id' => [2, 3, 4]])->asArray()->all();
$trial = Packets::findOne(['id' => 1]);


?>

<link href="/css/payment/payment.css" rel="stylesheet">

<script>
    $(document).ready(function () {
        $('.start-trial').click(function () {
            $('[data-bs-target="#logIn_SignUp"]').click()
            $('.signUp').click()
        })
    })
</script>


<div class="top-space"></div>
<div id="packets" class="container" style="max-width: 829px;">
    <div class="row">
        <div class="col-12" align="center">
            <h1>
                <?= Yii::$app->devSet->getTranslate('selectPlan') ?>
            </h1>
        </div>
    </div>

    <div class="row bullet-points justify-content-between">
        <div class="col-sm-4 text-center">
            <span class="bullet-info">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 2C6.977 2 2.5 6.477 2.5 12C2.5 17.523 6.977 22 12.5 22C18.023 22 22.5 17.523 22.5 12C22.5 6.477 18.023 2 12.5 2ZM18.207 9.707L11.207 16.707C11.012 16.902 10.756 17 10.5 17C10.244 17 9.988 16.902 9.793 16.707L6.793 13.707C6.402 13.316 6.402 12.684 6.793 12.293C7.184 11.902 7.816 11.902 8.207 12.293L10.5 14.586L16.793 8.293C17.184 7.902 17.816 7.902 18.207 8.293C18.598 8.684 18.598 9.316 18.207 9.707Z" fill="#4FAE33"/>
                </svg>
                <?= Yii::$app->devSet->getTranslate('bullet1') ?>
            </span>
        </div>
        <div class="col-sm-4 text-center">
            <span class="bullet-info">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 2C6.977 2 2.5 6.477 2.5 12C2.5 17.523 6.977 22 12.5 22C18.023 22 22.5 17.523 22.5 12C22.5 6.477 18.023 2 12.5 2ZM18.207 9.707L11.207 16.707C11.012 16.902 10.756 17 10.5 17C10.244 17 9.988 16.902 9.793 16.707L6.793 13.707C6.402 13.316 6.402 12.684 6.793 12.293C7.184 11.902 7.816 11.902 8.207 12.293L10.5 14.586L16.793 8.293C17.184 7.902 17.816 7.902 18.207 8.293C18.598 8.684 18.598 9.316 18.207 9.707Z" fill="#4FAE33"/>
                </svg>
                <?= Yii::$app->devSet->getTranslate('bullet2') ?>
            </span>
        </div>
        <div class="col-sm-4 text-center">
            <span class="bullet-info">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 2C6.977 2 2.5 6.477 2.5 12C2.5 17.523 6.977 22 12.5 22C18.023 22 22.5 17.523 22.5 12C22.5 6.477 18.023 2 12.5 2ZM18.207 9.707L11.207 16.707C11.012 16.902 10.756 17 10.5 17C10.244 17 9.988 16.902 9.793 16.707L6.793 13.707C6.402 13.316 6.402 12.684 6.793 12.293C7.184 11.902 7.816 11.902 8.207 12.293L10.5 14.586L16.793 8.293C17.184 7.902 17.816 7.902 18.207 8.293C18.598 8.684 18.598 9.316 18.207 9.707Z" fill="#4FAE33"/>
                </svg>
                <?= Yii::$app->devSet->getTranslate('bullet3') ?> - <?= $trial[$currency] ?> <?= $currencyIcon ?>
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="packet-card">
                <h2>
                    <?= Yii::$app->devSet->getTranslate($packets[0]['nameKeyword']) ?>
                </h2>
                <div class="price-block display-flex">
                    <div>
                        <?php if ($packets[0]['discountPercent'] > 0) { ?>
                            <discount class="position-relative">
                                <div class="red-stripe position-absolute w-100"></div>
                                <?= $packets[0][$currency] ?><?= $currencyIcon ?>
                            </discount><br>
                        <?php } ?>

                        <price><?= round($packets[0][$currency] - ($packets[0][$currency] * $packets[0]['discountPercent']) / 100) ?></price><currency><?= $currencyIcon ?></currency>
                        <br>
                        <span><?= Yii::$app->devSet->getTranslate('monthlyL') ?></span>
                    </div>
                    <img src="/img/payment/walking.svg" alt="walking icon">
                </div>

                <div style="margin-bottom: 25px;border-top: 1px solid #D9DFE9;"></div>

                <div class="description-block">
                    <?= Yii::$app->devSet->getTranslate($packets[0]['descriptionKeyword']) ?>
                </div>

                <button type="button" class="btn start-trial">
                    <?= Yii::$app->devSet->getTranslate('selectAPlan') ?>
                </button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="packet-card position-relative popular">
                <div class="popular-badge position-absolute">
                    <?= Yii::$app->devSet->getTranslate('mostPopular') ?>
                </div>

                <h2>
                    <?= Yii::$app->devSet->getTranslate($packets[1]['nameKeyword']) ?>
                </h2>
                <div class="price-block display-flex">
                    <div>
                        <?php if ($packets[1]['discountPercent'] > 0) { ?>
                            <discount class="position-relative">
                                <div class="red-stripe position-absolute w-100"></div>
                                <?= $packets[1][$currency] ?><?= $currencyIcon ?>
                            </discount><br>
                        <?php } ?>

                        <price><?= round($packets[1][$currency] - ($packets[1][$currency] * $packets[0]['discountPercent']) / 100) ?></price><currency><?= $currencyIcon ?></currency>
                        <br>
                        <span><?= Yii::$app->devSet->getTranslate('monthlyL') ?></span>
                    </div>
                    <img src="/img/payment/running.svg" alt="running icon">
                </div>

                <div style="margin-bottom: 25px;border-top: 1px solid #D9DFE9;"></div>

                <div class="description-block">
                    <?= Yii::$app->devSet->getTranslate($packets[1]['descriptionKeyword']) ?>
                </div>

                <button type="button" class="btn start-trial">
                    <?= Yii::$app->devSet->getTranslate('selectAPlan') ?>
                </button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="packet-card">
                <h2>
                    <?= Yii::$app->devSet->getTranslate($packets[2]['nameKeyword']) ?>
                </h2>
                <div class="price-block display-flex">
                    <div>
                        <?php if ($packets[2]['discountPercent'] > 0) { ?>
                            <discount class="position-relative">
                                <div class="red-stripe position-absolute w-100"></div>
                                <?= $packets[2][$currency] ?><?= $currencyIcon ?>
                            </discount><br>
                        <?php } ?>

                        <price><?= round($packets[2][$currency] - ($packets[2][$currency] * $packets[2]['discountPercent']) / 100) ?></price><currency><?= $currencyIcon ?></currency>
                        <br>
                        <span><?= Yii::$app->devSet->getTranslate('monthlyL') ?></span>
                    </div>
                    <img src="/img/payment/rushing.svg" alt="rushing icon">
                </div>

                <div style="margin-bottom: 25px;border-top: 1px solid #D9DFE9;"></div>

                <div class="description-block">
                    <?= Yii::$app->devSet->getTranslate($packets[2]['descriptionKeyword']) ?>
                </div>

                <button type="button" class="btn start-trial">
                    <?= Yii::$app->devSet->getTranslate('selectAPlan') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="bottom-space"></div>





