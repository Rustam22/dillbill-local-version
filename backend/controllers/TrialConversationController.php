<?php

namespace backend\controllers;

use backend\models\Teachers;
use backend\models\TrialConversationUsers;
use backend\models\TrialConversationUsersSearch;
use DateInterval;
use DateTime;
use Google_Service_Calendar_EventDateTime;
use Yii;
use backend\models\TrialConversation;
use backend\models\TrialConversationSearch;
use yii\web\NotFoundHttpException;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * TrialConversationController implements the CRUD actions for TrialConversation model.
 */
class TrialConversationController extends AppController
{

    /**
     * Lists all TrialConversation models.
     * @return string
     */
    public function actionIndex(): string
    {    
        $searchModel = new TrialConversationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $trialUsersModelSearch = new TrialConversationUsersSearch();
        $trialUsersDataProvider = $trialUsersModelSearch->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            'trialUsersModelSearch' => $trialUsersModelSearch,
            'trialUsersDataProvider' => $trialUsersDataProvider,
        ]);
    }


    /**
     * Displays a single TrialConversation model.
     * @param integer $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "TrialConversation #".$id,
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
     * @throws NotFoundHttpException
     */
    public function actionViewTrialUsers($id): string
    {
        return $this->render('view-trial-users', [
            'model' => $this->findModelTrialUsers($id),
        ]);
    }

    /**
     * Creates a new TrialConversation model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new TrialConversation();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new TrialConversation",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            } else if ($model->load($request->post()) and $model->save()) {
                $endsAt = new DateTime($model->date.' '.$model->startsAt);
                $endsAt->modify('+1 hour');
                $model->endsAt = $endsAt->format('H:i');
                $model->save();

                if(!Yii::$app->devSet->isLocal()) {
                    $topicByDate = Yii::$app->devSet->todayTopic($model->level, $model->date);
                    $startDateTime = new DateTime($model->date . ' ' . $model->startsAt);
                    $endsAtDateTime = new DateTime($model->date . ' ' . $model->startsAt);
                    $endsAtDateTime->add(new DateInterval('PT' . 60 . 'M'));

                    try {
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            //CURLOPT_URL => 'https://tutor-management.bubbleapps.io/version-test/api/1.1/wf/trial_lesson/initialize',
                            //CURLOPT_URL => 'https://tutor.dillbill.com/version-test/api/1.1/wf/trial-lesson',
                            CURLOPT_URL => 'https://tutor.dillbill.com/api/1.1/wf/trial-lesson',
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
                                'Class_material: https://drive.google.com/file/d/1qNac7IlqnsllRiltsiPUAZ6Vi903ffna/view',
                                'Class_topic: Travel',
                                'Authorization: Bearer '
                            ),
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);
                    } catch (\Exception $exception) {}

                    try {
                        $model->tutorName = ($model->tutorName == null) ? $model->tutor->teacherName : $model->tutorName;
                        $model->tutorEmail = ($model->tutorEmail == null) ? $model->tutor->email : $model->tutorEmail;

                        $model->eventId = Yii::$app->googleCalendar->createEvent(
                            'DillBill Trial Lesson',
                            'Moderator: '.$model->tutorName.'<br> Topic: Travel',
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
                    'title'=> "Create new TrialConversation",
                    'content'=>'<span class="text-success">Create TrialConversation success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new TrialConversation",
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

    public function actionCreateTrialUsers()
    {
        $model = new TrialConversationUsers();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-trial-users', 'id' => $model->id]);
        }

        return $this->render('create-trial-users', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrialConversation model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return array|Response|string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdate(int $id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $previousTutorEmail = ($model->tutorEmail == null) ? $model->tutor->email : $model->tutorEmail;

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update TrialConversation #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            } else if ($model->load($request->post())) {
                $newTutorEmail = Teachers::findOne(['id' => $model->tutorId])->email;

                if (!Yii::$app->devSet->isLocal()) {
                    $startDateTime = new DateTime($model->date . ' ' . $model->startsAt);
                    $endsAtDateTime = new DateTime($model->date . ' ' . $model->startsAt);
                    $endsAtDateTime->add(new DateInterval('PT' . 60 . 'M'));

                    try {
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            //CURLOPT_URL => 'https://tutor.dillbill.com/version-test/api/1.1/wf/update-trial',
                            CURLOPT_URL => 'https://tutor-management.bubbleapps.io/api/1.1/wf/update-trial',
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
                                'Class_material: https://drive.google.com/file/d/1qNac7IlqnsllRiltsiPUAZ6Vi903ffna/view',
                                'Class_topic: Travel',
                                'Authorization: Bearer '
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
                    }  catch (\Exception $exception) {}
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
                    'title'=> "Update TrialConversation #".$id,
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
     * @throws NotFoundHttpException
     */
    public function actionUpdateTrialUsers(int $id)
    {
        $model = $this->findModelTrialUsers($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-trial-users', 'id' => $model->id]);
        }

        return $this->render('update-trial-users', [
            'model' => $model,
        ]);
    }

    /**
     * Delete an existing TrialConversation model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $this->findModel($id)->delete();

        if (!Yii::$app->devSet->isLocal()) {
            try {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://tutor-management.bubbleapps.io/api/1.1/wf/delete-trial',
                    //CURLOPT_URL => 'https://tutor-management.bubbleapps.io/version-test/api/1.1/wf/delete-trial',
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
            } catch (\Exception $exception) {}

            try {
                Yii::$app->googleCalendar->eventDelete($model->eventId);
            }  catch (\Exception $exception) {}
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

    public function actionDeleteTrialUsers(int $id): Response
    {
        $this->findModelTrialUsers($id)->delete();

        return $this->redirect(['index']);
    }

     /**
     * Delete multiple existing TrialConversation model.
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
     * Finds the TrialConversation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrialConversation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrialConversation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelTrialUsers(int $id)
    {
        if (($model = TrialConversationUsers::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
