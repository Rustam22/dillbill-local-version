<?php


namespace frontend\controllers;

use backend\models\ConversationUsers;
use backend\models\Feedback;
use common\models\UserParameters;
use common\models\UserProfile;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


class DashboardController extends AppController {

    private $context = array();
    public $layout = 'dash';

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'logout', 'login', 'sign-up', 'boarding', 'request-password-reset',
                    'reset-password', 'my-classes', 'grammar', 'class-history',
                    'time-zone-assign', 'google-calendar', 'level-test', 'time-availability',
                    'confirm-start-date', 'confirm-phone-number', 'feedback-confirm'
                ],
                'rules' => [
                    [
                        'actions' => ['login', 'sign-up', 'request-password-reset', 'reset-password'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'logout', 'boarding', 'my-classes', 'grammar',
                            'time-zone-assign', 'google-calendar', 'class-history',
                            'level-test', 'time-availability', 'confirm-start-date', 'confirm-phone-number',
                            'feedback-confirm'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    Yii::$app->response->redirect(['landing/index']);
                },
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],

        ];
    }


    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'dash'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    public function actionTimeZoneAssign()
    {
        if(Yii::$app->request->isAjax) {
            if(Yii::$app->request->post()) {
                $timeZone = Yii::$app->request->post('timeZone');

                $userParameters = UserParameters::findOne(['userId' => Yii::$app->user->id]);
                $userProfile = UserProfile::findOne(['userId' => Yii::$app->user->id]);

                if($userParameters->currentLevel != 'empty') {
                    $userParameters->availability = null;
                    $userParameters->availabilityLCD = null;
                }

                if (in_array($timeZone, DateTimeZone::listIdentifiers())) {
                    $userProfile->timezone = $timeZone;
                } else {
                    $userProfile->timezone = Yii::$app->timeZone;
                }

                $userProfile->save(false);
                $userParameters->save(false);

                Yii::$app->devSet->segmentAction(Yii::$app->user->id, 'Time Zone Assign');

                return true;
            }
        }
    }


    public function actionFeedbackConfirm() {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {
                $classId = Yii::$app->request->post('classId');
                $topic = Yii::$app->request->post('topic');
                $starsCount = Yii::$app->request->post('starsCount');
                $comment = Yii::$app->request->post('comment');

                if (strlen($comment) > 5000) {
                    return json_encode([
                        'success' => false,
                        'error' => 'False text format'
                    ]);
                }

                if (is_numeric($starsCount) and ($starsCount >= 1 and $starsCount <= 5)) {
                    $conversationUsers = ConversationUsers::find()->where([
                        'userId' => Yii::$app->user->id,
                        'action' => 'enter'
                    ])->orderBy(['conversationDate' => SORT_DESC])->limit(1)->one();

                    if ($conversationUsers->conversation->id != $classId) {
                        return json_encode([
                            'success' => false,
                            'error' => 'Wrong class id'
                        ]);
                    }

                    if (Feedback::find()->where(['classId' => $conversationUsers->conversation->id])->exists()) {
                        return json_encode([
                            'success' => false,
                            'error' => 'Feedback already exists'
                        ]);
                    }

                    $feedback = new Feedback();

                    $feedback->userId = Yii::$app->user->id;
                    $feedback->classId = $classId;
                    $feedback->tutorId = $conversationUsers->conversation->teacher->id;
                    $feedback->topic = $topic;
                    $feedback->score = $starsCount;
                    $feedback->comment = $comment;

                    if ($feedback->save(false)) {
                        $score = Feedback::find()->where(['userId' => Yii::$app->user->id, 'tutorId' => $feedback->tutorId])->sum('score');
                        $count = Feedback::find()->where(['userId' => Yii::$app->user->id, 'tutorId' => $feedback->tutorId])->count();
                        $average = round($score / $count, 2);

                        if (!Yii::$app->devSet->isLocal()) {
                            try {
                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => 'https://tutor-management.bubbleapps.io/api/1.1/wf/feedback-adults/',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_HTTPHEADER => array(
                                        'average: '.$average,
                                        'score: '.$score,
                                        'comment: '.$feedback->comment,
                                        'conversationID: '.$conversationUsers->conversation->id,
                                        'tutorEMAIL: '.$conversationUsers->conversation->teacher->email,
                                        'userEMAIL: '.Yii::$app->user->identity->email,
                                        'Authorization: Bearer '
                                    ),
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                            } catch (Exception $exception) {}
                        }

                        sleep(1);

                        return json_encode([
                            'success' => true,
                            'score' => $score,
                            'count' => $count,
                            'average' => $average,
                            'error' => ''
                        ]);
                    } else {
                        return json_encode([
                            'success' => false,
                            'error' => 'System error, please contact support.'
                        ]);
                    }
                } else {
                    return json_encode([
                        'success' => false,
                        'error' => 'False number format'
                    ]);
                }
            }
        }
    }


    /**
     * @throws Exception
     */
    public function actionConfirmStartDate()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {
                $chosenStartClassDate = Yii::$app->request->post('startClassDate');
                $currentDate = new DateTime('now');

                $userParameters = UserParameters::findOne(['userId' => Yii::$app->user->id]);
                $userProfile = UserProfile::findOne(['userId' => Yii::$app->user->id]);

                if($userParameters->proficiency == 'no') {
                    return json_encode(['success' => false, 'error' => 'Wrong request']);
                }

                if(strlen($chosenStartClassDate) != 10) {
                    return json_encode(['success' => false, 'error' => 'Date format is incorrect']);
                }

                // Check if chosen date is acceptable
                $nextNClasses = 3;
                $schedulesArray  = array_map('intval', str_split($userParameters->currentSchedule));
                $today = new DateTime('now');
                $startDate = ($userParameters->startDate != null) ? new DateTime($userParameters->startDate) : $today;
                $startDate = ($startDate < $today) ? $today : $startDate;

                $nextAvailableDates = array();

                while (sizeof($nextAvailableDates) < $nextNClasses) {
                    if(in_array($startDate->format('w'), $schedulesArray)) {
                        $nextAvailableDates[] = $startDate->format('Y-m-d');
                    }

                    $startDate->add(new DateInterval('P1D'));
                }

                // Critical time synchronization
                $userDateTime = Yii::$app->devSet->getDateByTimeZone($userProfile->timezone);
                $chosenStartClassDate = new DateTime($chosenStartClassDate.' '.$userDateTime->format('H:i:s'));
                $adjustedStartClassDate = Yii::$app->devSet->adjustedDateTimeToSystemTimeZone($chosenStartClassDate, $userProfile->timezone);

                $unixTimestamp = new DateTime($chosenStartClassDate->format('Y-m-d H:i:s'));
                //$unixTimestamp = new DateTime('2022-02-12 00:00:00');
                //$unixTimestamp->add(new DateInterval('PT' . 30 . 'M'));

                if (!in_array($adjustedStartClassDate->format('Y-m-d'), $nextAvailableDates)) {
                    return json_encode(['success' => false, 'error' => 'Chosen date is not acceptable']);
                }

                // Increase the balance on the selected start date
                $d1 = new DateTime('now 00:00:00');
                $d2 = new DateTime($chosenStartClassDate->format('Y-m-d'));
                $interval = $d1->diff($d2);
                $diffInDays = $interval->d;

                $userParameters->cpBalance = $userParameters->cpBalance + $diffInDays;
                $userParameters->startDate = $adjustedStartClassDate->format('Y-m-d');
                $userParameters->proficiency = 'no'; // no

                $userParameters->save(false);

                try {
                    $reservedClasses = ConversationUsers::deleteAll(
                        '`userId` = '.Yii::$app->user->id.' AND `conversationDate` >= "'.$currentDate->format('Y-m-d').'" AND `action` = "reserve"'
                    );
                } catch (Exception $exception) {}

                $userStartTime = explode( '-', $userParameters->availability)[0];
                $userEndTime   = explode( '-', $userParameters->availability)[1];
                $homeLandTimeStart = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$userStartTime);
                $homeLandTimeEnd = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$userEndTime);
                $userLandTimeStart = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($homeLandTimeStart, $userProfile->timezone);
                $userLandTimeEnd = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($homeLandTimeEnd, $userProfile->timezone);

                $chosenTimeRange = $userLandTimeStart->format('H:i').'-'.$userLandTimeEnd->format('H:i');

                try {
                    Yii::$app->acc->createClassesForNewUser(    // Create classes for new user
                        $userParameters->currentLevel,
                        $userParameters->availability,
                        $userParameters->currentSchedule,
                        $userParameters->startDate
                    );
                } catch (Exception $exception) {}

                if (!Yii::$app->devSet->isLocal()) {
                    try {
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS =>'{
                            "to": "'.Yii::$app->user->identity->email.'",
                            "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['startDateApprove'].'",
                            "message_data": {
                                "TimeRange": "'.$chosenTimeRange.'",
                                "StartDate": "'.$chosenStartClassDate->format('Y-m-d').'",
                                "email": "'.Yii::$app->user->identity->email.'",
                                "name": "'.$userProfile->name.'"
                            },
                            "identifiers": {
                                "id": "'.Yii::$app->user->id.'"
                            }
                        }',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer ',
                                'Content-Type: application/json'
                            ),
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                    }   catch (Exception $exception) {}

                    try {
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => '{
                                "to": "'.Yii::$app->user->identity->email.'",
                                "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['scheduledFirstClassDay'].'",
                                "send_at": '.$unixTimestamp->getTimestamp().',
                                "message_data": {
                                    "name": "'.$userProfile->name.'",
                                    "email": "'.Yii::$app->user->identity->email.'",
                                    "TimeRange": "'.$chosenTimeRange.'"
                                },
                                "identifiers": {
                                    "id": "'.Yii::$app->user->id.'"
                                }
                            }',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer ',
                                'Content-Type: application/json'
                            ),
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                    } catch (Exception $exception) {}
                }

                return json_encode([
                    'success' => true,
                    'error' => '',
                    'chosenStartDate' => $chosenStartClassDate->format('Y-m-d'),
                    'adjustedStartClassDate' => $adjustedStartClassDate->format('Y-m-d H:i:s'),
                    'chosenTimeRange' => $chosenTimeRange,
                    'unixTimestamp' => $unixTimestamp->format('Y-m-d H:i:s'),
                    'userDateTime' => $userDateTime->format('H:i:s')
                ]);
            }
        }
    }


    public function actionConfirmPhoneNumber() {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {
                $phoneNumber = trim(Yii::$app->request->post('phoneNumber'));

                if (strlen($phoneNumber) > 20 OR strlen($phoneNumber) < 7) {
                    return json_encode(['success' => false, 'error' => 'Wrong phone number']);
                }

                $userProfile = UserProfile::findOne(['userId' => Yii::$app->user->id]);
                $userProfile->phone = $phoneNumber;

                if ($userProfile->save(false)) {
                    return json_encode(['success' => true, 'error' => '']);
                } else {
                    return json_encode(['success' => false, 'error' => 'Phone not saved']);
                }
            }
        }
    }


    public function actionGoogleCalendar()
    {
        if(Yii::$app->request->isAjax) {
            if(Yii::$app->request->post()) {
                $action = Yii::$app->request->post('action');

                $userParameters = UserParameters::findOne(['userId' => Yii::$app->user->id]);

                if($action == 'connect') {
                    $gmail = Yii::$app->request->post('gmail');
                    list($person, $domain) = explode('@', $gmail);

                    if((strlen($person) < 3) OR ($domain != 'gmail.com')) {
                        return json_encode(['success' => false, 'error' => 'invalid gmail']);
                    }

                    $userParameters->googleCalendar = 'yes';
                    $userParameters->calendarGmail = $gmail;

                    if($userParameters->save(false)) {
                        return json_encode(['success' => true, 'error' => '']);
                    } else {
                        return json_encode(['success' => false, 'error' => 'System fail, please contact support!']);
                    }
                } elseif ($action == 'disconnect') {
                    $userParameters->googleCalendar = 'no';
                    $userParameters->calendarGmail = null;

                    if($userParameters->save(false)) {
                        return json_encode(['success' => true, 'error' => '']);
                    } else {
                        return json_encode(['success' => false, 'error' => 'System fail, please contact support!']);
                    }
                }

                return json_encode(['success' => false, 'error' => 'Wrong action']);
            }
        }
    }


    public function actionMyClasses()
    {
        if(
            (Yii::$app->user->identity->userParameters->currentLevel == 'empty') AND
            (Yii::$app->user->identity->userProfile->preliminaryLevel == null OR Yii::$app->user->identity->userProfile->timezone == null)
        ) {
            return $this->redirect(['dashboard/boarding']);
        }

        $context = [];
        Yii::$app->devSet->segmentAction(Yii::$app->user->id, 'My Class');

        return $this->render('my-classes', $context);
    }


    public function actionLevelTest()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {

                if(Yii::$app->user->identity->userParameters->cp == 0) {
                    return json_encode(['message' => 'User is not subscribed']);
                }

                if((Yii::$app->user->identity->userParameters->currentLevel == 'empty') AND (Yii::$app->user->identity->userParameters->cp == 0)) {
                    return json_encode(['message' => 'User level is not determined']);
                }

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://test.dillbill.com/api/1.1/wf/dillbill-user',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => array(
                        'username: '.Yii::$app->user->identity->userProfile->name.' '.Yii::$app->user->identity->userProfile->surname,
                        'email: '.Yii::$app->user->identity->email,
                        'password: bubble_secret_password_'.Yii::$app->user->identity->userParameters->promoCode,
                        'Authorization: Bearer '
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                return $response;
            }
        }
    }


    public function actionTimeAvailability()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();
                $currentDate = new DateTime('now');

                $weekdays = $post['weekdays'];
                $timeRange = $post['timeRange'];

                $availableWeekdays = [1, 2, 3, 4, 5, 6];
                $timeRanges = ['09:00-12:00', '15:00-18:00', '18:00-21:00', '21:00-00:00', '21:00-23:59', '21:00-24:00'];
                $lessons = strlen(Yii::$app->user->identity->userParameters->currentSchedule);

                if ($weekdays == '' OR $timeRange == '') {
                    return json_encode([
                        'success' => false,
                        'error' => 'Weekdays or time range is not selected.',
                    ]);
                }

                if (sizeof($weekdays) > $lessons) {
                    return json_encode([
                        'success' => false,
                        'error' => 'Selected packet does not include '.$lessons.' lessons',
                    ]);
                }

                foreach ($weekdays as $key => $value) {
                    if (!in_array($value, $availableWeekdays)) {
                        return json_encode([
                            'success' => false,
                            'error' => 'Selected weekdays does not exist',
                        ]);
                    }
                }

                if (!in_array($timeRange, $timeRanges)) {
                    return json_encode([
                        'success' => false,
                        'error' => 'Selected time range does not exist',
                    ]);
                }

                if (Yii::$app->user->identity->userParameters->currentLevel == 'empty') {
                    return json_encode([
                        'success' => false,
                        'error' => 'Choose a packet for schedule approval.',
                    ]);
                }

                if (Yii::$app->user->identity->getCpBalanceByUser(Yii::$app->user->id) <= 1) {
                    return json_encode([
                        'success' => false,
                        'error' => 'You do not have enough balance to approve the timetable.',
                    ]);
                }

                $userParameters = UserParameters::findOne(['userId' => Yii::$app->user->id]);

                $userParameters->availability = $timeRange;
                $userParameters->currentSchedule = implode('', $weekdays);
                $userParameters->availabilityLCD = date('Y-m-d');
                $userParameters->proficiency = (Yii::$app->user->identity->getCpBalanceByUser(Yii::$app->user->id) > 1) ? 'start-date' : 'no';

                if($userParameters->save(false)) {
                    Yii::$app->devSet->segmentAction(Yii::$app->user->id, 'Slot Change');

                    $exceptionMessage_1 = '';
                    $exceptionMessage_2 = '';

                    try {
                        $reservedClasses = ConversationUsers::deleteAll(
                            "`userId` = ".Yii::$app->user->id." AND `conversationDate` >= '".$currentDate->format('Y-m-d')."' AND `action` = 'reserve'"
                        );
                    } catch (Exception $exception) {
                        $exceptionMessage_1 = $exception->getMessage();
                    }

                    /****** Delete redundant classes ******/
                    try {
                        Yii::$app->acc->deleteRedundantLessons([Yii::$app->user->identity->userParameters->currentLevel]);
                    } catch (Exception $exception) {
                        $exceptionMessage_2 = $exception->getMessage();
                    }

                    return json_encode([
                        'success' => true,
                        'error' => '',
                        'message_1' => $exceptionMessage_1,
                        'message_2' => $exceptionMessage_2
                    ]);
                } else {
                    return json_encode([
                        'success' => false,
                        'error' => 'System error, please contact support',
                    ]);
                }

            }
        }
    }


    public function actionGrammar(): string
    {
        $context = [];
        Yii::$app->devSet->segmentAction(Yii::$app->user->id, 'Grammar');

        return $this->render('grammar', $context);
    }


    public function actionBoarding()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post('batch');

                $batch = [
                    'source' => ['friends-family', 'google-search', 'instagram', 'youtube', 'tiktok', 'billboard', 'news', 'other'],
                    'aim' => ['foreign-resources', 'new-job-opportunities', 'studying-abroad', 'moving-another-country', 'travel', 'other'],
                    'level' => ['beginner', 'elementary', 'pre-intermediate', 'intermediate', 'upper-intermediate'],
                    'timezone' => DateTimeZone::listIdentifiers()
                ];

                foreach ($post as $key => $value) {
                    if (!isset($batch[$key])) {
                        return json_encode(['success' => false, 'error' => 'Question does not exist']);
                    }

                    if (!in_array($value, $batch[$key])) {
                        return json_encode(['success' => false, 'error' => 'One of the selected answers does not exist']);
                    }
                }

                $userProfile = UserProfile::findOne(['userId' => Yii::$app->user->id]);

                $userProfile->source = $post['source'];
                $userProfile->aim = $post['aim'];
                $userProfile->preliminaryLevel = $post['level'];
                $userProfile->timezone = $post['timezone'];

                $userProfile->save(false);

                return json_encode(['success' => true, 'error' => $post]);
            }
        }

        if(
            Yii::$app->user->identity->userParameters->currentLevel != 'empty' OR
            (Yii::$app->user->identity->userProfile->preliminaryLevel != null AND Yii::$app->user->identity->userProfile->timezone != null)
        ) {
            return $this->redirect(['dashboard/my-classes']);
        }

        $context = [];
        Yii::$app->devSet->segmentAction(Yii::$app->user->id, 'Boarding');

        return $this->render('boarding', $context);
    }


    public function actionClassHistory(): string
    {
        $context = [];
        Yii::$app->devSet->segmentAction(Yii::$app->user->id, 'Class History');

        return $this->render('class-history', $context);
    }


}