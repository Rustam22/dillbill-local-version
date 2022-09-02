<?php


namespace frontend\controllers;
use Yii;
use yii\web\Controller;

Yii::$app->setTimeZone('Asia/Baku');

if(Yii::$app->devSet->getDevSet('cashFlush') == '1') {
    Yii::$app->cache->flush();
}

class AppController extends Controller {



}