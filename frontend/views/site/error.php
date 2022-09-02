<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;

?>


<style>
    .page-404 h1 {
        font-size: 126px;
        line-height: 125px;
        font-weight: 500;
        color: black;
    }
    .page-404 h2 {
        font-size: 30px;
        font-weight: 500;
        color: black;
    }
    .page-404 p {
        font-size: 16px;
        font-weight: 400;
        color: black;
    }
    .margin-top {
        margin-top: 80px;
    }
    .live-chat {
        box-shadow: 0 0 0 .25rem rgba(13,110,253,.25);
    }
    @media screen and (max-width: 741px) {
        .margin-top {
            margin-top: 40px !important;
        }
    }
</style>

<div class="margin-top"></div>

<div class="container w-100 page-404" style="max-width: 1010px;">
    <div class="row justify-content-between">
        <div class="col-sm-6">
            <h1 class="text-align-center">
                <?= Yii::$app->response->statusCode ?>
            </h1>
            <div style="margin-bottom: 50px;"></div>
            <?= Html::encode($message) ?>
            <br><br>
        </div>

        <div class="col-sm-6">
            <div class="row">
                <div class="col-12">
                    <h2 class="">
                        <?= nl2br(Html::encode($message)) ?>
                    </h2>
                    <div style="margin-top: 10px;"></div>
                    <p>
                        <?= Yii::$app->devSet->getTranslate('thePageMightBeRemoved') ?>
                    </p>
                    <div style="margin-top: 20px;"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    <a href="<?= Yii::$app->request->hostInfo; ?>">
                        <button type="button" class="btn btn-primary h6-700 button_blue_faq border-radius-28 w-100" style="margin-bottom: 20px;">
                            <?= Yii::$app->devSet->getTranslate('viewHomePage') ?>
                        </button>
                    </a>
                </div>
                <div class="col-lg-5">
                    <button type="button" class="live-chat btn h6-400 number_faq border-radius-28 grays_700 w-100">
                        <?= Yii::$app->devSet->getTranslate('contactSupport') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<br><br><br><br><br><br><br>
