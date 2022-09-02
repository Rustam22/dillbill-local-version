<?php


namespace frontend\controllers;

use backend\models\Teachers;
use Yii;



class LandingController extends AppController {

    public $layout = 'landing';

    /**
     * Displays landing page.
     *
     */
    public function actionIndex()
    {
        if(!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard/my-classes']);
        }

        $context = [];

        return $this->render('landing', $context);
    }


    public function actionPrices(): string
    {
        $context = [];

        return $this->render('prices', $context);
    }


    public function actionAboutUs(): string
    {
        $context = [];

        return $this->render('aboutUs', $context);
    }


    public function actionContactUs(): string
    {
        $context = [];

        return $this->render('contactUs', $context);
    }

    public function actionBusiness(): string
    {
        $context = [];

        return $this->render('business', $context);
    }
}