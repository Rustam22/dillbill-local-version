<?php

namespace backend\controllers;


use Yii;

class StatisticsController extends AppController
{


    public $layout = false;

    /**
     * Lists all Conversation models.
     * @return string
     */
    public function actionIndex(): string
    {


        return $this->render('index', ['executionTime' => false]);
    }


}