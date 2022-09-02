<?php

namespace frontend\controllers;

use backend\models\Conversation;
use backend\models\ConversationUsers;
use backend\models\Teachers;
use backend\models\TrialConversation;
use common\models\User;
use common\models\UserParameters;
use common\models\UserProfile;
use DateTime;
use Exception;
use Yii;
use yii\web\Controller;


class ApiController extends Controller
{

    private $user = 'dillbill@biryerde.az';
    private $password = 'Hidrogen!9913400352282';
    public  $enableCsrfValidation = false;

    public function behaviors(): array
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // restrict access to
                    'Origin' => [''.Yii::$app->request->hostInfo.''],
                    // Allow only POST and PUT methods
                    'Access-Control-Request-Method' => ['POST', 'PUT'],
                    // Allow only headers 'X-Wsse'
                    'Access-Control-Request-Headers' => ['X-Wsse'],
                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                    'Access-Control-Allow-Credentials' => true,
                    // Allow OPTIONS caching
                    'Access-Control-Max-Age' => 3600,
                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],
                'actions' => [
                    'incoming' => [
                        'Origin' => [''.Yii::$app->request->hostInfo.''],
                        'Access-Control-Request-Method' => ['POST', 'PUT'],
                        'Access-Control-Request-Headers' => ['*'],
                        'Access-Control-Allow-Credentials' => null,
                        'Access-Control-Max-Age' => 3600,
                        'Access-Control-Expose-Headers' => [],
                    ],
                ],
            ],
        ];
    }


    public function actionTutorAssign(): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }

        if($_SERVER['PHP_AUTH_USER'] == $this->user AND $_SERVER['PHP_AUTH_PW'] == $this->password) {
            $request = Yii::$app->request->post();

            if (!isset($request['conversationId']) OR !isset($request['tutorEmail'])) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid parameters'];
            }

            if (!filter_var($request['tutorEmail'], FILTER_VALIDATE_EMAIL)) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid email'];
            }

            if (!is_numeric($request['conversationId'])) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid conversation id'];
            }

            $conversation = Conversation::findOne(['id' => $request['conversationId']]);
            $tutor = Teachers::findOne(['email' => $request['tutorEmail']]);

            if (($conversation == null) OR ($tutor == null)) {
                Yii::$app->response->statusCode = 404;
                return ['status' => false, 'message' => 'Tutor or conversation not found'];
            }

            $conversation->tutorId = $tutor->id;

            if ($conversation->save(false)) {
                if(!Yii::$app->devSet->isLocal()) {
                    try {
                        Yii::$app->googleCalendar->addAttendee($conversation->eventId, $tutor->email);

                        $topicByDate = Yii::$app->devSet->todayTopic($conversation->level, $conversation->date);
                        $description = 'Moderator: '.$tutor->teacherName.'<br> Topic: '.$topicByDate['description'].', '.$topicByDate['type'];

                        Yii::$app->googleCalendar->updateDescription($conversation->eventId, $description);
                    } catch (\Exception $exception) {}
                }

                return ['status' => true, 'message' => 'Teacher assigned successfully'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => false, 'message' => 'DB error'];
            }

        } else {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }

    }


    public function actionTrialTutorAssign(): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }

        if($_SERVER['PHP_AUTH_USER'] == $this->user AND $_SERVER['PHP_AUTH_PW'] == $this->password) {
            $request = Yii::$app->request->post();

            if (!isset($request['conversationId']) OR !isset($request['tutorEmail'])) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid parameters'];
            }

            if (!filter_var($request['tutorEmail'], FILTER_VALIDATE_EMAIL)) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid email'];
            }

            if (!is_numeric($request['conversationId'])) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid conversation id'];
            }

            $conversation = TrialConversation::findOne(['id' => $request['conversationId']]);
            $tutor = Teachers::findOne(['email' => $request['tutorEmail']]);

            if (($conversation == null) OR ($tutor == null)) {
                Yii::$app->response->statusCode = 404;
                return ['status' => false, 'message' => 'Tutor or conversation not found'];
            }

            $conversation->tutorId = $tutor->id;

            if ($conversation->save(false)) {
                if(!Yii::$app->devSet->isLocal()) {
                    try {
                        Yii::$app->googleCalendar->addAttendee($conversation->eventId, $tutor->email);
                        $description = 'Moderator: '.$tutor->teacherName.'<br> Topic: Travel';

                        Yii::$app->googleCalendar->updateDescription($conversation->eventId, $description);
                    } catch (\Exception $exception) {}
                }

                return ['status' => true, 'message' => 'Teacher assigned successfully'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => false, 'message' => 'DB error'];
            }

        } else {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }

    }


    public function actionTutorCancel(): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }

        if ($_SERVER['PHP_AUTH_USER'] == $this->user AND $_SERVER['PHP_AUTH_PW'] == $this->password) {
            $request = Yii::$app->request->post();

            if (!isset($request['conversationId']) OR !isset($request['tutorEmail'])) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid parameters'];
            }

            if (!filter_var($request['tutorEmail'], FILTER_VALIDATE_EMAIL)) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid email'];
            }

            if (!is_numeric($request['conversationId'])) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid conversation id'];
            }

            $conversation = Conversation::findOne(['id' => $request['conversationId']]);

            if ($conversation == null) {
                Yii::$app->response->statusCode = 404;
                return ['status' => false, 'message' => 'Conversation not found'];
            }

            $conversation->tutorId = 87;
            $tutor = Teachers::findOne(['id' => $conversation->tutorId]);

            if ($conversation->save(false)) {
                if(!Yii::$app->devSet->isLocal()) {
                    try {
                        Yii::$app->googleCalendar->deleteAttendee($request['tutorEmail'], $conversation->eventId);

                        $topicByDate = Yii::$app->devSet->todayTopic($conversation->level, $conversation->date);
                        $description = 'Moderator: '.$tutor->teacherName.'<br> Topic: '.$topicByDate['description'].', '.$topicByDate['type'];

                        Yii::$app->googleCalendar->updateDescription($conversation->eventId, $description);
                    } catch (\Exception $exception) {}
                }

                return ['status' => true, 'message' => 'Teacher canceled successfully'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => false, 'message' => 'DB error'];
            }

        } else {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }
    }


    public function actionTrialTutorCancel(): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }

        if ($_SERVER['PHP_AUTH_USER'] == $this->user AND $_SERVER['PHP_AUTH_PW'] == $this->password) {
            $request = Yii::$app->request->post();

            if (!isset($request['conversationId']) OR !isset($request['tutorEmail'])) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid parameters'];
            }

            if (!filter_var($request['tutorEmail'], FILTER_VALIDATE_EMAIL)) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid email'];
            }

            if (!is_numeric($request['conversationId'])) {
                Yii::$app->response->statusCode = 406;
                return ['status' => false, 'message' => 'Invalid conversation id'];
            }

            $conversation = TrialConversation::findOne(['id' => $request['conversationId']]);

            if ($conversation == null) {
                Yii::$app->response->statusCode = 404;
                return ['status' => false, 'message' => 'Conversation not found'];
            }

            $conversation->tutorId = 87;
            $tutor = Teachers::findOne(['id' => $conversation->tutorId]);

            if ($conversation->save(false)) {
                if(!Yii::$app->devSet->isLocal()) {
                    try {
                        Yii::$app->googleCalendar->deleteAttendee($request['tutorEmail'], $conversation->eventId);
                        $description = 'Moderator: '.$tutor->teacherName.'<br> Topic: Travel';

                        Yii::$app->googleCalendar->updateDescription($conversation->eventId, $description);
                    } catch (\Exception $exception) {}
                }

                return ['status' => true, 'message' => 'Teacher canceled successfully'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => false, 'message' => 'DB error'];
            }

        } else {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }
    }


    public function actionChangeLevel(): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }

        if ($_SERVER['PHP_AUTH_USER'] == $this->user AND $_SERVER['PHP_AUTH_PW'] == $this->password) {
            $request = Yii::$app->request->post();

            $levels = [
                'beginner' => true,
                'elementary' => true,
                'pre-intermediate' => true,
                'intermediate' => true,
                'upper-intermediate' => true,
                'advanced' => true
            ];

            if (!isset($request['email']) OR !isset($request['level'])) {
                Yii::$app->response->statusCode = 406;

                return ['status' => false, 'message' => 'Invalid parameters'];
            }

            if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                Yii::$app->response->statusCode = 406;

                return ['status' => false, 'message' => 'Invalid email'];
            }

            if (!$levels[$request['level']]) {
                Yii::$app->response->statusCode = 406;

                return ['status' => false, 'message' => 'Invalid level'];
            }

            $user = User::findOne(['email' => $request['email']]);

            if ($user == null) {
                Yii::$app->response->statusCode = 406;

                return ['status' => false, 'message' => 'User is not found'];
            }

            if ($user->userParameters->currentLevel == 'empty') {
                $userParameters = UserParameters::findOne(['userId' => $user->id]);
                $userProfile = UserProfile::findOne(['userId' => $user->id]);

                $userParameters->proficiency = 'level';
                $userParameters->currentLevel = $request['level'];

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
                                            "to": "'.$user->email.'",
                                            "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID['en']['levelResult'].'",
                                            "message_data": {
                                                "level": "'.ucfirst($request['level']).'",
                                                "name": "'.$userProfile->name.'",
                                                "email": "'.$user->email.'"
                                            },
                                            "identifiers": {
                                                "id": "'.$user->id.'"
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

                return ['status' => true, 'message' => 'Level determined successfully'];
            }

            if ($user->userParameters->currentLevel != 'empty') {
                $userBalance = (new \common\models\User())->getCpBalanceByUser($user->id);
                $userParameters = UserParameters::findOne(['userId' => $user->id]);
                $userProfile = UserProfile::findOne(['userId' => $user->id]);
                $currentDate = new DateTime('now');

                if ($userBalance > 0) {
                    $reservedClasses = ConversationUsers::deleteAll(
                        "`userId` = ".$user->id." AND `conversationDate` >= '".$currentDate->format('Y-m-d')."' AND `action` = 'reserve'"
                    );

                    /****** Delete redundant classes ******/
                    try {
                        Yii::$app->acc->deleteRedundantLessons([$userParameters->currentLevel, $request['level']]);
                    } catch (Exception $exception) {
                        debug($exception->getMessage());
                    }

                    $userParameters->currentLevel = $request['level'];

                    $places = Yii::$app->acc->possiblePlaces(
                        [$userParameters->currentLevel],
                        $userParameters->availability,
                        $currentDate->format('Y-m-d')
                    );

                    $startDate = ($places == 0) ? Yii::$app->acc->calculateStartDate($userParameters->currentSchedule)[1] : $currentDate->format('Y-m-d');

                    $userParameters->startDate = $startDate;
                    $userParameters->proficiency = 'start-date';
                }

                $userParameters->currentLevel = $request['level'];
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
                                            "to": "'.$user->email.'",
                                            "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID['en']['levelResult'].'",
                                            "message_data": {
                                                "level": "'.ucfirst($request['level']).'",
                                                "name": "'.$userProfile->name.'",
                                                "email": "'.$user->email.'"
                                            },
                                            "identifiers": {
                                                "id": "'.$user->id.'"
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

                return ['status' => true, 'message' => 'Level changed successfully'];
            }

        } else {
            header("WWW-Authenticate: Basic realm=\"Private Area\" ");
            Yii::$app->response->statusCode = 401;

            return ['status' => false, 'message' => 'Sorry, you need proper credentials'];
        }
    }

}