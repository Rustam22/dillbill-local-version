<?php

namespace backend\controllers;

use backend\models\Teachers;
use DateInterval;
use DateTime;
use Google_Service_Calendar_EventDateTime;
use Yii;
use backend\models\Conversation;
use backend\models\ConversationSearch;
use yii\web\NotFoundHttpException;
use \yii\web\Response;
use yii\helpers\Html;


/**
 * ConversationController implements the CRUD actions for Conversation model.
 */
class ConversationController extends AppController
{

    /**
     * Lists all Conversation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ConversationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Conversation model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "Conversation #".$id,
                'content'=>$this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                    Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Conversation model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Conversation();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Conversation",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($model->load($request->post()) and $model->save()) {

                if(!Yii::$app->devSet->isLocal()) {
                    $topicByDate = Yii::$app->devSet->todayTopic($model->level, $model->date);
                    $startDateTime = new DateTime($model->date . ' ' . $model->startsAt);
                    $endsAtDateTime = new DateTime($model->date . ' ' . $model->startsAt);
                    $endsAtDateTime->add(new DateInterval('PT' . 60 . 'M'));

                    try {
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://tutor.dillbill.com/api/1.1/wf/lesson',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_HTTPHEADER => array(
                                'Start_time: '.$startDateTime->format( 'm/d/Y g:i A').'',
                                'End_time: '.$endsAtDateTime->format( 'm/d/Y g:i A').'',
                                'Class_id: '.$model->id.'',
                                'Class_level: '.$model->level.'',
                                'Class_material: '.$topicByDate['url'].'',
                                'Class_topic: '.$topicByDate['description'].'',
                                'Authorization: Bearer '
                            ),
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);
                    } catch (\Exception $exception) {}

                    try {
                        $model->tutorName = ($model->tutorName == null) ? $model->teacher->teacherName : $model->tutorName;
                        $model->tutorEmail = ($model->tutorEmail == null) ? $model->teacher->email : $model->tutorEmail;

                        $model->eventId = Yii::$app->googleCalendar->createEvent(
                            'DillBill Lesson',
                            'Moderator: '.$model->tutorName.'<br> Topic: '.$topicByDate['description'].'',
                            'Asia/Baku',
                            date('c', strtotime(date(''.$model->date.' '.$model->startsAt))),
                            date('c', strtotime(date(''.$model->date.' '.$model->endsAt)))
                        );

                        if($model->tutorEmail != null) {
                            Yii::$app->googleCalendar->addAttendee($model->eventId, $model->tutorEmail);
                        }
                    } catch (\Exception $exception) {}
                }

                $model->save();

                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Conversation",
                    'content'=>'<span class="text-success">Create Conversation success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

                ];
            }else{
                return [
                    'title'=> "Create new Conversation",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }

    }

    /**
     * Updates an existing Conversation model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return array|Response|string|void
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $previousTutorEmail = ($model->tutorEmail == null) ? $model->teacher->email : $model->tutorEmail;

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Conversation #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post())) {
                $newTutorEmail = Teachers::findOne(['id' => $model->tutorId])->email;

                if(!Yii::$app->devSet->isLocal()) {
                    $topicByDate = Yii::$app->devSet->todayTopic($model->level, $model->date);
                    $startDateTime = new DateTime($model->date . ' ' . $model->startsAt);
                    $endsAtDateTime = new DateTime($model->date . ' ' . $model->startsAt);
                    $endsAtDateTime->add(new DateInterval('PT' . 60 . 'M'));

                    try {
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://tutor-management.bubbleapps.io/api/1.1/wf/update',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_HTTPHEADER => array(
                                'Start_time: '.$startDateTime->format( 'm/d/Y g:i A').'',
                                'End_time: '.$endsAtDateTime->format( 'm/d/Y g:i A').'',
                                'Class_id: '.$model->id.'',
                                'Class_level: '.$model->level.'',
                                'Class_material: '.$topicByDate['url'].'',
                                'Class_topic: '.$topicByDate['description'].'',
                                'Authorization: Bearer'
                            ),
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);
                    } catch (\Exception $exception) {}

                    try {
                        Yii::$app->googleCalendar->deleteAttendee($previousTutorEmail, $model->eventId);
                        Yii::$app->googleCalendar->addAttendee($model->eventId, $newTutorEmail);

                        $startDateTime = new Google_Service_Calendar_EventDateTime();
                        $endDateTime = new Google_Service_Calendar_EventDateTime();

                        $startDateTime->dateTime = date('c', strtotime(date(''.$model->date.' '.$model->startsAt)));
                        $endDateTime->dateTime = date('c', strtotime(date(''.$model->date.' '.$model->endsAt)));
                        $startDateTime->timeZone = 'Asia/Baku';
                        $endDateTime->timeZone = 'Asia/Baku';

                        Yii::$app->googleCalendar->updateClassTime(
                            $model->eventId,
                            $startDateTime,
                            $endDateTime
                        );
                    } catch (\Exception $exception) {}
                }

                if($model->save()) {
                    return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'title'=> "Conversation #".$id,
                        'content'=>$this->renderAjax('view', [
                            'model' => $model,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                    ];
                }
            }else{
                return [
                    'title'=> "Update Conversation #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing Conversation model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if(!Yii::$app->devSet->isLocal()) {
            try {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://tutor-management.bubbleapps.io/api/1.1/wf/delete',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => array(
                        'Class_id: '.$model->id.'',
                        'Authorization: Bearer '
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
            } catch (\Exception $exception) {

            }

            try {
                Yii::$app->googleCalendar->eventDelete($model->eventId);
            }  catch (\Exception $exception) {
            }
        }

        $model->delete();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

    /**
     * Delete multiple existing Conversation model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkdelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);

            if(!Yii::$app->devSet->isLocal()) {
                try {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://tutor-management.bubbleapps.io/api/1.1/wf/delete',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_HTTPHEADER => array(
                            'Class_id: '.$model->id.'',
                            'Authorization: Bearer '
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                } catch (\Exception $exception) {

                }

                try {
                    Yii::$app->googleCalendar->eventDelete($model->eventId);
                }  catch (\Exception $exception) {
                }
            }

            $model->delete();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }

    }

    /**
     * Finds the Conversation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Conversation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Conversation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
