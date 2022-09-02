<?php

namespace backend\controllers;


use Exception;
use Yii;

class ControlPanelController extends AppController
{

    /**
     * Lists all Conversation models.
     * @return string
     */
    public function actionIndex(): string
    {

        /*$responseComposeLessons = Yii::$app->acc->possiblePlaces(
            ['pre-intermediate'],
            '18:00-21:00',
            '2022-02-16'
        );

        $startDate = Yii::$app->acc->calculateStartDate(246);
        debug($startDate);*/

        return $this->render('index', ['executionTime' => false]);
    }


    /**
     * Creates a new SocketUsers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string
     */
    public function actionCreate(): string
    {
        $levels = explode(',', Yii::$app->request->post('levels'));
        $schedules = explode(',', Yii::$app->request->post('schedules'));
        $timeRanges = explode(',', Yii::$app->request->post('timeRanges'));
        $exceptionalAccounts = explode(',', Yii::$app->request->post('exceptionalAccounts'));

        $levels = array_map('trim', $levels);
        $schedules = array_map('trim', $schedules);
        $timeRanges = array_map('trim', $timeRanges);
        $exceptionalAccounts = array_map('trim', $exceptionalAccounts);

        $start = microtime(true);
        $responseComposeLessons = Yii::$app->acc->composeLessons(
            $levels,
            $timeRanges,
            $exceptionalAccounts
        );

        //try {
            Yii::$app->acc->deleteRedundantLessons($levels, $timeRanges, $exceptionalAccounts);
        //} catch (Exception $exception) {
            //debug($exception->getMessage());
        //}
        $time_elapsed_secs = microtime(true) - $start;

        echo '<br><br><br>';
        debug($responseComposeLessons);

        return $this->render('index', [
            'executionTime' => $time_elapsed_secs
        ]);
    }

}