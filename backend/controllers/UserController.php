<?php

namespace backend\controllers;

use backend\models\ConversationUsers;
use common\models\UserParameters;
use common\models\UserProfile;
use DateTime;
use Exception;
use Yii;
use backend\models\User;
use backend\models\UserSearch;
use yii\web\NotFoundHttpException;
use \yii\web\Response;
use yii\helpers\Html;




/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AppController
{

    /**
     * Lists all User models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single User model.
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
                'title'=> "User #".$id,
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
     * Creates a new User model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return array|Response|string|string[]
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;

        $model = new User();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new User",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new User",
                    'content'=>'<span class="text-success">Create User success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

                ];
            }else{
                return [
                    'title'=> "Create new User",
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
     * Updates an existing User model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): array
    {
        $request = Yii::$app->request;

        $model = $this->findModel($id);
        $userProfile = UserProfile::findOne(['userId' => $model->id]);
        $userParameters = UserParameters::findOne(['userId' => $model->id]);

        if($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($request->isGet){
                return [
                    'title'=> "Update User #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            } else {
                $preLevel = $model->userParameters->currentLevel;
                $postLevel = $request->post()['UserParameters']['currentLevel'];
                $preTimeRange = $model->userParameters->availability;
                $postTimeRange = $request->post()['UserParameters']['availability'];
                $preSchedule = $model->userParameters->currentSchedule;
                $postSchedule = $request->post()['UserParameters']['currentSchedule'];

                $model->email = $request->post()['User']['email'];

                $userProfile->color = $request->post()['UserProfile']['color'];
                $userProfile->name = $request->post()['UserProfile']['name'];
                $userProfile->surname = $request->post()['UserProfile']['surname'];
                $userProfile->timezone = $request->post()['UserProfile']['timezone'];

                $userParameters->availability = $request->post()['UserParameters']['availability'];
                $userParameters->availabilityLCD = $request->post()['UserParameters']['availabilityLCD'];
                $userParameters->currentLevel = $request->post()['UserParameters']['currentLevel'];
                $userParameters->startDate = $request->post()['UserParameters']['startDate'];
                $userParameters->confirmed = $request->post()['UserParameters']['confirmed'];
                $userParameters->proficiency = $request->post()['UserParameters']['proficiency'];
                $userParameters->currentSchedule = $request->post()['UserParameters']['currentSchedule'];
                $userParameters->cp = $request->post()['UserParameters']['cp'];
                $userParameters->cpBalance = $request->post()['UserParameters']['cpBalance'];
                $userParameters->lpd = $request->post()['UserParameters']['lpd'];
                $userParameters->googleCalendar = $request->post()['UserParameters']['googleCalendar'];
                $userParameters->calendarGmail = $request->post()['UserParameters']['calendarGmail'];


                /**________________________  Level, Time Range Change START  ________________________**/
                if (($preTimeRange != $postTimeRange) and ($postTimeRange != null) OR ($preSchedule != $postSchedule) and ($postSchedule != null)) {
                    $userBalance = (new \common\models\User())->getCpBalanceByUser($model->id);
                    $currentDate = new DateTime('now');

                    if ($userBalance > 0) {
                        $reservedClasses = ConversationUsers::deleteAll(
                            "`userId` = ".$model->id." AND `conversationDate` >= '".$currentDate->format('Y-m-d')."' AND `action` = 'reserve'"
                        );

                        /****** Delete redundant classes ******/
                        try {
                            Yii::$app->acc->deleteRedundantLessons([$preLevel, $postLevel]);
                        } catch (Exception $exception) {
                            debug($exception->getMessage());
                        }

                        $places = Yii::$app->acc->possiblePlaces(
                            [$userParameters->currentLevel],
                            $userParameters->availability,
                            $currentDate->format('Y-m-d')
                        );

                        $startDate = ($places == 0) ? Yii::$app->acc->calculateStartDate($userParameters->currentSchedule)[1] : $currentDate->format('Y-m-d');

                        $userParameters->startDate = $startDate;
                        $userParameters->proficiency = 'start-date';

                        $model->save(false);
                        $userParameters->save(false);
                    }
                }

                if ($preLevel == 'empty' and $postLevel != 'empty') {
                    $userParameters->proficiency = 'level';

                    $model->save(false);
                    $userParameters->save(false);

                    if (!Yii::$app->devSet->isLocal()) {
                        try {
                            $curl = curl_init();    // Send Proficiency Level Email

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => '
                                        {
                                            "to": "'.$model->email.'",
                                            "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID['en']['levelResult'].'",
                                            "message_data": {
                                                "level": "'.ucfirst($postLevel).'",
                                                "name": "'.$userProfile->name.'",
                                                "email": "'.$model->email.'"
                                            },
                                            "identifiers": {
                                                "id": "'.$model->id.'"
                                            }
                                        }',
                                CURLOPT_HTTPHEADER => array(
                                    'Authorization: Bearer ',
                                    'Content-Type: application/json'
                                ),
                            ));

                            $response = curl_exec($curl);
                            curl_close($curl);
                        } catch (\Exception $exception) {}
                    }
                }

                if (($preLevel != $postLevel) and ($postLevel != null) and ($postLevel != 'empty') and ($preLevel != 'empty')) {
                    $userBalance = (new \common\models\User())->getCpBalanceByUser($model->id);
                    $currentDate = new DateTime('now');

                    if ($userBalance > 0) {
                        $reservedClasses = ConversationUsers::deleteAll(
                            "`userId` = ".$model->id." AND `conversationDate` >= '".$currentDate->format('Y-m-d')."' AND `action` = 'reserve'"
                        );

                        /****** Delete redundant classes ******/
                        //try {
                            Yii::$app->acc->deleteRedundantLessons([$preLevel, $postLevel]);
                        //} catch (Exception $exception) {
                            //debug($exception->getMessage());
                        //}

                        $places = Yii::$app->acc->possiblePlaces(
                            [$userParameters->currentLevel],
                            $userParameters->availability,
                            $currentDate->format('Y-m-d')
                        );

                        $startDate = ($places == 0) ? Yii::$app->acc->calculateStartDate($userParameters->currentSchedule)[1] : $currentDate->format('Y-m-d');

                        $userParameters->startDate = $startDate;
                        $userParameters->proficiency = 'level-start-date';

                        $model->save(false);
                        $userParameters->save(false);

                        if (!Yii::$app->devSet->isLocal()) {
                            try {
                                $curl = curl_init();    // Send Proficiency Level Email

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => '
                                            {
                                                "to": "'.$model->email.'",
                                                "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID['en']['levelResult'].'",
                                                "message_data": {
                                                    "level": "'.ucfirst($postLevel).'",
                                                    "name": "'.$userProfile->name.'",
                                                    "email": "'.$model->email.'"
                                                },
                                                "identifiers": {
                                                    "id": "'.$model->id.'"
                                                }
                                            }',
                                    CURLOPT_HTTPHEADER => array(
                                        'Authorization: Bearer ',
                                        'Content-Type: application/json'
                                    ),
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                            } catch (\Exception $exception) {}
                        }
                    }
                }
                /**________________________  Level, Time Range Change END  ________________________**/


                Yii::$app->devSet->segmentAction($model->id, 'User Update');

                $model->save(false);
                $userProfile->save(false);
                $userParameters->save(false);

                $model = $this->findModel($id);     // renew model for ajax view

                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "User #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
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
     * Delete an existing User model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

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
     * Delete multiple existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
