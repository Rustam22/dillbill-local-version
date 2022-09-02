<?php

namespace common\components;

use backend\models\Conversation;
use common\models\UserParameters;
use DateInterval;
use DateTime;
use Exception;
use Yii;


class AutomaticClassCreationComponent {

    const NEXT_N_DAYS = 7;
    const GROUP_SIZE = 4;

    const EXCLUDED_USERS = [
        'rustam.atakisiev@gmail.com',
        'emil.hasanli.91@gmail.com',
        'sanan@dillbill.net',
        'khasiyev.farid@gmail.com',
        'fidan.jafarzadeh@gmail.com',
        'jalyaabbakirova@gmail.com'
    ];

    const TIME_RANGES = [
        '09:00-12:00' => ['09:00-12:00'],
        '15:00-18:00' => ['15:00-18:00'],
        '18:00-21:00' => ['18:00-21:00'],
        '21:00-00:00' => ['21:00-00:00', '21:00-23:59', '21:00-24:00'],
        '21:00-23:59' => ['21:00-00:00', '21:00-23:59', '21:00-24:00'],
        '21:00-24:00' => ['21:00-00:00', '21:00-23:59', '21:00-24:00'],
    ];

    const EMAIL_MESSAGES_ID = [
        'en' => [
            'confirmEmail' => 17,
            'welcome' => 23,
            'resetPassword' => 20,
            'levelResult' => 18,
            'startDateApprove' => 24,
            'scheduledFirstClassDay' => 22,
            'trialBookApprove' => 25,
            'trialNotificationInTwoHours' => 29,
            'trialNotificationInFiveMinutes' => 30
        ],
        'az' => [
            'confirmEmail' => 17,
            'welcome' => 23,
            'resetPassword' => 20,
            'levelResult' => 18,
            'startDateApprove' => 24,
            'scheduledFirstClassDay' => 22,
            'trialBookApprove' => 25,
            'trialNotificationInTwoHours' => 29,
            'trialNotificationInFiveMinutes' => 30
        ],
        'ru' => [
            'confirmEmail' => 17,
            'welcome' => 23,
            'resetPassword' => 20,
            'levelResult' => 18,
            'startDateApprove' => 24,
            'scheduledFirstClassDay' => 22,
            'trialBookApprove' => 25,
            'trialNotificationInTwoHours' => 29,
            'trialNotificationInFiveMinutes' => 30
        ],
        'tr' => [
            'confirmEmail' => 17,
            'welcome' => 23,
            'resetPassword' => 20,
            'levelResult' => 18,
            'startDateApprove' => 24,
            'scheduledFirstClassDay' => 22,
            'trialBookApprove' => 25,
            'trialNotificationInTwoHours' => 29,
            'trialNotificationInFiveMinutes' => 30
        ],
        'pt' => [
            'confirmEmail' => 17,
            'welcome' => 23,
            'resetPassword' => 20,
            'levelResult' => 18,
            'startDateApprove' => 24,
            'scheduledFirstClassDay' => 22,
            'trialBookApprove' => 25,
            'trialNotificationInTwoHours' => 29,
            'trialNotificationInFiveMinutes' => 30
        ],
    ];


    /**
     * @throws Exception
     */
    public function createClassForSpecificSegment($level, $timeRange, $schedule, $dateArray = []) {
        foreach ($dateArray as $date) {
            $weekDay = (new DateTime($date))->format('w');

            $a = UserParameters::find()->
            select(["(`userParameters`.`cpBalance` - DATEDIFF('$date', `userParameters`.`lpd`)) AS balance"])->
            innerJoin('user', '`userParameters`.`userId` = `user`.`id`')->
            andWhere(['>', '`userParameters`.`cp`', 0])->
            andWhere(['>', '`userParameters`.`lpd`', 0])->
            andWhere(['not in', '`user`.`email`', self::EXCLUDED_USERS])->
            andWhere([
                '`userParameters`.`currentLevel`' => $level,
                '`userParameters`.`availability`' => self::TIME_RANGES[$timeRange]
            ])->
            andWhere(['like', '`userParameters`.`currentSchedule`', $weekDay])->
            andWhere(['<=', '`userParameters`.`startDate`', $date])->
            having(['>', 'balance', 0])->
            count();

            $classStartTime = explode( '-', self::TIME_RANGES[$timeRange][0])[0];
            $classEndTime   = explode( '-', self::TIME_RANGES[$timeRange][0])[1];
            $classStartTime = ($classStartTime == '00:00') ? '24:00' : $classStartTime;
            $classEndTime   = ($classEndTime == '00:00') ? '24:00' : $classEndTime;

            $c = Conversation::find()->
            where(['date' => $date, 'visible' => 'yes', 'level' => $level])->
            andWhere(['>=', 'startsAt', $classStartTime])->
            andWhere(['<', 'startsAt', $classEndTime])->
            count();

            $createClass = ((self::GROUP_SIZE * $c - $a) <= 0) ? 1 : 0;
            //debug($date.' - '.$a.' - '.$c.' - createClass = '.$createClass);

            if ($createClass) {
                $classDateTime = new DateTime($date.' '.$classStartTime);
                $hourRange = explode(':', $classEndTime)[0] - explode(':', $classStartTime)[0];

                $conversation = new Conversation();

                $conversation->level = $level;
                $conversation->date = $date;

                $minutes_to_add = 60;
                $randomStartTime = rand(0, $hourRange);
                $classDateTimeCopy = new DateTime($classDateTime->format('Y-m-d H:i'));
                $conversation->startsAt = $classDateTimeCopy->add(new DateInterval('PT' . ($minutes_to_add * $randomStartTime - (($randomStartTime == $hourRange) ? 60 : 0)) . 'M'))->format('H:i');
                $classDateTimeCopy->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                $conversation->endsAt = $classDateTimeCopy->format('H:i');

                $conversation->tutorId = 87;
                $conversation->visible = 'yes';

                /***________________________  API Call Start ________________________***/
                $topicByDate = Yii::$app->devSet->todayTopic($conversation->level, $conversation->date);
                $startDateTime = new DateTime($conversation->date . ' ' . $conversation->startsAt);
                $endsAtDateTime = new DateTime($conversation->date . ' ' . $conversation->startsAt);
                $endsAtDateTime->add(new DateInterval('PT' . $minutes_to_add . 'M'));

                if(!Yii::$app->devSet->isLocal()) {     // Google calendar data send
                    try {
                        $conversation->eventId = Yii::$app->googleCalendar->createEvent(
                            'DillBill Lesson',
                            'Moderator: Teacher will be assigned<br> Topic: ' . $topicByDate['description'] . ', ' . $topicByDate['type'],
                            'Asia/Baku',
                            date('c', strtotime(date('' . $startDateTime->format('Y-m-d H:i')))),
                            date('c', strtotime(date('' . $endsAtDateTime->format('Y-m-d H:i'))))
                        );
                    } catch (\Exception $exception) {}
                }

                $conversation->save(false);

                if(!Yii::$app->devSet->isLocal()) {    // TM data send
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
                            'Start_time: '.$startDateTime->format( 'm/d/Y g:i A'),
                            'End_time: '.$endsAtDateTime->format( 'm/d/Y g:i A'),
                            'Class_id: '.$conversation->id,
                            'Class_level: '.$conversation->level,
                            'Class_material: '.$topicByDate['url'],
                            'Class_topic: '.$topicByDate['description'],
                            'Authorization: Bearer '
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                }
                /***________________________  API Call End ________________________***/
            }
        }
    }


    /**
     * @throws Exception
     */
    public function createClassesForNewUser($userLevel, $userTimeRange, $userSchedule, $userStartDate) {
        $startDate = new DateTime($userStartDate);
        $schedulesArray = array_map('intval', str_split($userSchedule));
        $nextDaysArray = [];

        $counter = 0;
        while($counter < 3) {
            if (in_array($startDate->format('w'), $schedulesArray)) {
                $nextDaysArray[] = $startDate->format('Y-m-d');
                $counter++;
            }

            $startDate->modify('+1 day');
        }

        $this->createClassForSpecificSegment($userLevel, $userTimeRange, $userSchedule, $nextDaysArray);
    }



    public function calculateStartDate($schedule): array
    {
        $schedulesArray = array_map('intval', str_split($schedule));
        $startDateTime = new DateTime('now');

        $currentWeekDay = $startDateTime->format('w');
        $startWeekDay = false;

        for ($j = 0; $j < sizeof($schedulesArray); $j++) {
            $scheduleArray = $schedulesArray[$j];

            if ($currentWeekDay < $scheduleArray) {
                $startWeekDay = $scheduleArray;
                break;
            }

            if ($currentWeekDay == $scheduleArray) {
                $startWeekDay = $schedulesArray[($j + 1) % sizeof($schedulesArray)];
                break;
            }
        }

        $startWeekDay = ($startWeekDay == false) ? $schedulesArray[0] : $startWeekDay;

        while ((int)$startWeekDay != (int)$startDateTime->format('w')) {
            $startDateTime->modify('+1 day');
        }

        return [$startWeekDay, $startDateTime->format('Y-m-d')];
    }


    /**
     * @throws Exception
     */
    public function possiblePlaces($level, $range, $date): int
    {
        $dateObject = new DateTime($date);

        /*
            SELECT user.email, (`userParameters`.`cpBalance` - DATEDIFF('2022-04-21', `userParameters`.`lpd`)) AS `balance`
            FROM `userParameters` INNER JOIN `user` ON `userParameters`.`userId` = `user`.`id`
            WHERE (`userParameters`.`cp` > 0)
            AND (`userParameters`.`lpd` > 0)
            AND (`user`.`email` NOT IN ('rustam.atakisiev@gmail.com', 'emil.hasanli.91@gmail.com', 'sanan@dillbill.net', 'khasiyev.farid@gmail.com', 'fidan.jafarzadeh@gmail.com', 'jalyaabbakirova@gmail.com'))
            AND ((`userParameters`.`currentLevel`='pre-intermediate')
            AND (`userParameters`.`availability`='15:00-18:00'))
            AND (`userParameters`.`startDate` <= '2022-04-21')
            AND (`userParameters`.`currentSchedule` LIKE '%4%')
            HAVING `balance` > 0;
         */
        $activeUsersAmount = UserParameters::find()->
        select(["(`userParameters`.`cpBalance` - DATEDIFF('$date', `userParameters`.`lpd`)) AS balance"])->
        innerJoin('user', '`userParameters`.`userId` = `user`.`id`')->
        andWhere(['>', '`userParameters`.`cp`', 0])->
        andWhere(['>', '`userParameters`.`lpd`', 0])->
        andWhere(['not in', '`user`.`email`', self::EXCLUDED_USERS])->
        andWhere([
            '`userParameters`.`currentLevel`' => $level,
            '`userParameters`.`availability`' => self::TIME_RANGES[$range][0]
        ])->
        andWhere(['like', '`userParameters`.`currentSchedule`', $dateObject->format("w") ])->
        andWhere(['<=', '`userParameters`.`startDate`', $date])->
        having(['>', 'balance', 0])->
        count();

        $userStartTime = explode( '-', self::TIME_RANGES[$range][0])[0];
        $userEndTime   = explode( '-', self::TIME_RANGES[$range][0])[1];
        $userStartTime = ($userStartTime == '00:00') ? '24:00' : $userStartTime;
        $userEndTime = ($userEndTime == '00:00') ? '24:00' : $userEndTime;

        $existingClassesAmount = Conversation::find()->
        where(['date' => $date, 'visible' => 'yes', 'level' => $level])->
        andWhere(['>=', 'startsAt', $userStartTime])->
        andWhere(['<', 'startsAt', $userEndTime])->
        count();

        $emptyPlaces = (self::GROUP_SIZE * (int)$existingClassesAmount) - (int)$activeUsersAmount;
        $emptyPlaces = ($emptyPlaces < 0) ? 0 : $emptyPlaces;

       /* echo '<br><br><br><br>';
        echo ' _activeUsersAmount: '.$activeUsersAmount.'<br>';
        echo ' _existingClassesAmount: '.$existingClassesAmount.'<br>';
        echo ' _emptyPlaces: '.$emptyPlaces.'<br>';
        debug([(int)$activeUsersAmount, (int)$existingClassesAmount, $emptyPlaces]);
        debug($range);
        debug(self::TIME_RANGES[$range][0]);*/

        return (int)$emptyPlaces;
    }


    public function deleteRedundantLessons($levels = [], $ranges = self::TIME_RANGES, $notFor = self::EXCLUDED_USERS) {
        $availableTimeRanges = array();
        $ranges = self::TIME_RANGES;
        foreach ($ranges as $k => $v) { $availableTimeRanges[] = $k; }

        //debug($availableTimeRanges);

        $activeUsers = UserParameters::find()->
        select(['`user`.`email`', '`userParameters`.`currentLevel`', '`userParameters`.`currentSchedule`',
            '`userParameters`.`availability`', '(`userParameters`.`cpBalance` - DATEDIFF(NOW(), `userParameters`.`lpd`)) AS balance'])->
        innerJoin('user', '`userParameters`.`userId` = `user`.`id`')->
        andWhere(['>', '`userParameters`.`cp`', 0])->
        andWhere(['>', '`userParameters`.`lpd`', 0])->
        andWhere(['not in', '`user`.`email`', $notFor])->
        andWhere([
            '`userParameters`.`currentLevel`' => $levels,
            '`userParameters`.`availability`' => $availableTimeRanges
        ])->
        andWhere('NOW() >= `userParameters`.`startDate`')->
        having(['>', 'balance', 0])->
        orderBy([
            '`userParameters`.`currentLevel`' => SORT_ASC,
            '`userParameters`.`currentSchedule`' => SORT_ASC,
            '`userParameters`.availability' => SORT_ASC
        ])->
        asArray()->
        all();

        //debug($activeUsers);

        $weekdays = [1, 2, 3, 4, 5, 6];
        $localDateTime = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').' 00:00:00');
        $lastScheduledDate = Yii::$app->db->createCommand("SELECT MAX(date) AS `lcd` FROM `conversation`")->queryOne()['lcd'];
        $lastScheduledDate = new DateTime($lastScheduledDate.' 00:00:00');
        $upcomingDays = date_diff($lastScheduledDate, $localDateTime)->d;

        foreach ($levels as $level) {
            foreach ($weekdays as $weekday) {
                foreach ($availableTimeRanges as $range) {
                    for ($day = 0; $day < $upcomingDays; $day++) {
                        $attendeesAmount = 0;
                        $nextDays = new $localDateTime;
                        $nextDays->add(new DateInterval('P'.$day.'D'));

                        // If next day's week day matches with schedule
                        if ($weekday == $nextDays->format('w')) {
                            $userStartTime = explode( '-', $range)[0];
                            $userEndTime = explode( '-', $range)[1];
                            $userStartTime = ($userStartTime == '00:00') ? '24:00' : $userStartTime;
                            $userEndTime = ($userEndTime == '00:00') ? '24:00' : $userEndTime;

                            $existingClassesAmount = Conversation::find()->
                            where(['date' => $nextDays->format('Y-m-d'), 'visible' => 'yes', 'level' => $level])->
                            andWhere(['>=', 'startsAt', $userStartTime])->
                            andWhere(['<', 'startsAt', $userEndTime])->
                            count();

                            // Calculate attendees amount for each segment [level -> schedule -> range]
                            foreach ($activeUsers as $key => $user) {
                                if (in_array($range, self::TIME_RANGES[$user['availability']]) and str_contains($user['currentSchedule'], $weekday) and $user['currentLevel'] == $level) {
                                    if ($day < $user['balance']) {
                                        $attendeesAmount++;
                                    }
                                }
                            }

                            $necessaryLessonsAmount = (int)($attendeesAmount / self::GROUP_SIZE) + (($attendeesAmount % self::GROUP_SIZE) > 0 ? 1 : 0);

                            if ($existingClassesAmount > $necessaryLessonsAmount) {    // Redundant class is detected
                                /*
                                    SELECT `conversation`.`id`, `conversation`.`level`, `conversation`.`date`, `conversation`.`startsAt`,  `conversation`.`eventId`, `conversationUsers`.`id`, `conversationUsers`.`userId`, `conversationUsers`.`userName`, `conversationUsers`.`userEmail`, `conversationUsers`.`tutorName`
                                    FROM `conversation`
                                    LEFT JOIN `conversationUsers` ON `conversationUsers`.`conversationId` = `conversation`.`id`
                                    WHERE `conversationUsers`.`userId` IS NULL
                                    AND `conversation`.`date` = '2022-07-04'
                                    AND `conversation`.`level` = 'elementary'
                                    AND `conversation`.`visible` = 'yes'
                                    AND `conversation`.`startsAt` >= '18:00'
                                    AND `conversation`.`startsAt` < '21:00';
                                 */

                                $redundantClasses = Conversation::find()->
                                select(['`conversation`.`id`', '`conversation`.`level`', '`conversation`.`date`',
                                    '`conversation`.`startsAt`', '`conversation`.`eventId`', '`conversationUsers`.`userId`',
                                    '`conversationUsers`.`userName`', '`conversationUsers`.`userEmail`', '`conversationUsers`.`tutorName`'])->
                                leftJoin('conversationUsers', '`conversationUsers`.`conversationId` = `conversation`.`id`')->
                                where([
                                    '`conversation`.`visible`' => 'yes',
                                    '`conversation`.`date`' => $nextDays->format('Y-m-d'),
                                    '`conversation`.`level`' => $level,
                                ])->
                                andWhere(['is', '`conversationUsers`.`userId`', null])->
                                andWhere(['>=', '`conversation`.`startsAt`', $userStartTime])->
                                andWhere(['<', '`conversation`.`startsAt`', $userEndTime])->
                                all();

                                foreach ($redundantClasses as $redundantClass) {
                                    $classId = $redundantClass['id'];
                                    $eventId = $redundantClass['eventId'];

                                    //echo 'redundant<br>';
                                    /*debug($classId);
                                    debug($eventId);
                                    debug($redundantClass);*/

                                    if($redundantClass->delete()) {
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
                                                        'Class_id: '.$classId.'',
                                                        'Authorization: Bearer '
                                                    ),
                                                ));

                                                $response = curl_exec($curl);
                                                curl_close($curl);
                                            } catch (\Exception $exception) {

                                            }

                                            try {
                                                Yii::$app->googleCalendar->eventDelete($eventId);
                                            }  catch (\Exception $exception) {
                                            }
                                        }
                                    }
                                }


                            }

                            /*echo 'classes: '.$existingClassesAmount.'<br>';
                            echo 'users: '.$attendeesAmount.'<br>';
                            echo 'level: '.$level.'<br>';
                            echo 'weekD: '.$weekday.'<br>';
                            echo 'range: '.$range.'<br>';
                            echo 'date: '.$nextDays->format('Y-m-d').'<br>';
                            echo 'necessaryLessons: '.$necessaryLessonsAmount.'<br><br>';*/

                        }
                    }
                }
            }
        }
    }


    /**
     * @throws Exception
     */
    public function composeLessons($levels = [], $ranges = [], $notFor = self::EXCLUDED_USERS): array
    {
        $activeUsers = UserParameters::find()->
        select(['`user`.`email`', '`userParameters`.`currentLevel`', '`userParameters`.`currentSchedule`',
            '`userParameters`.`availability`', '(`userParameters`.`cpBalance` - DATEDIFF(NOW(), `userParameters`.`lpd`)) AS balance'])->
        innerJoin('user', '`userParameters`.`userId` = `user`.`id`')->
        andWhere(['>', '`userParameters`.`cp`', 0])->
        andWhere(['>', '`userParameters`.`lpd`', 0])->
        andWhere(['not in', '`user`.`email`', $notFor])->
        andWhere([
            '`userParameters`.`currentLevel`' => $levels,
            '`userParameters`.`availability`' => $ranges
        ])->
        andWhere('NOW() >= `userParameters`.`startDate`')->
        having(['>', 'balance', 0])->
        orderBy([
            '`userParameters`.`currentLevel`' => SORT_ASC,
            '`userParameters`.`currentSchedule`' => SORT_ASC,
            '`userParameters`.availability' => SORT_ASC
        ])->
        asArray()->
        all();

        /*
            SELECT `user`.`email`, `userParameters`.`currentLevel`, `userParameters`.`currentSchedule`, `userParameters`.`availability`, (`userParameters`.`cpBalance` - DATEDIFF(NOW(), `userParameters`.`lpd`)) AS `balance`
            FROM `userParameters`
            INNER JOIN `user` ON `userParameters`.`userId` = `user`.`id`
            WHERE (`userParameters`.`cp` > 0) AND (`userParameters`.`lpd` > 0)
            AND (`user`.`email` NOT IN ('rustam.atakisiev@gmail.com', 'emil.hasanli.91@gmail.com', 'sanan@dillbill.net', 'khasiyev.farid@gmail.com', 'fidan.jafarzadeh@gmail.com', 'jalyaabbakirova@gmail.com'))
            AND ((`userParameters`.`currentLevel` in ('intermediate', 'pre-intermediate', 'elementary', 'beginner'))
            AND (`userParameters`.`currentSchedule` IN ('135', '246', '123456'))
            AND (`userParameters`.`availability` IN ('21:00-00:00', '21:00-23:59', '21:00-24:00', '18:00-21:00', '09:00-12:00', '15:00-18:00'))) HAVING `balance` > 0
            ORDER BY `userParameters`.`currentLevel`, `userParameters`.`currentSchedule`, `userParameters`.`availability`;
         * */

        $creatableClasses = [];
        $weekdays = [1, 2, 3,  4, 5, 6];
        $localDateTime = Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone());
        //echo '<br><br><br>';

        foreach ($levels as $level) {
            foreach ($weekdays as $weekday) {
                foreach ($ranges as $range) {
                    for ($day = 0; $day < self::NEXT_N_DAYS; $day++) {
                        $attendeesAmount = 0;
                        $nextDays = new $localDateTime;
                        $nextDays->add(new DateInterval('P'.$day.'D'));

                        // If next day's week day matches with schedule
                        if ($weekday == $nextDays->format('w')) {
                            $userStartTime = explode( '-', $range)[0];
                            $userEndTime = explode( '-', $range)[1];
                            $userStartTime = ($userStartTime == '00:00') ? '24:00' : $userStartTime;
                            $userEndTime = ($userEndTime == '00:00') ? '24:00' : $userEndTime;

                            $existingClassesAmount = Conversation::find()->
                            where(['date' => $nextDays->format('Y-m-d'), 'visible' => 'yes', 'level' => $level])->
                            andWhere(['>=', 'startsAt', $userStartTime])->
                            andWhere(['<', 'startsAt', $userEndTime])->
                            count();

                            // Calculate attendees amount for each segment [level -> schedule -> range]
                            foreach ($activeUsers as $key => $user) {
                                if (str_contains($user['currentSchedule'], $weekday) and $user['currentLevel'] == $level and $user['availability'] == $range) {
                                    if ($day < $user['balance']) {
                                        $attendeesAmount++;
                                    }
                                }
                            }

                            // Calculate creatable classes amount for each upcoming day
                            $classAmountForGivenDay = (int)($attendeesAmount / self::GROUP_SIZE) + (($attendeesAmount % self::GROUP_SIZE) > 0 ? 1 : 0);
                            $classesToBeCreated = (($classAmountForGivenDay - $existingClassesAmount) < 0) ? 0 : ($classAmountForGivenDay - $existingClassesAmount);

                            /*if($nextDays->format('Y-m-d') == '2022-03-08') {
                                echo '<br><br><br>';
                                echo 'Current date: '.$nextDays->format('Y-m-d').'<br>';
                                echo 'Current level: '.$level.'<br>';
                                echo 'Current schedule: '.$schedule.'<br>';
                                echo 'Current range: '.$range.'<br>';
                                echo 'Existing class amount: '.$existingClassesAmount.'<br>';
                                echo 'Attendees amount: '.$attendeesAmount.'<br>';
                                echo 'Class amount for given date: '.$classAmountForGivenDay.'<br>';
                                echo 'Classes to be created: '.$classesToBeCreated.'<br>';
                            }*/

                            // Compose Class Creation
                            if ($classesToBeCreated > 0) {
                                $class = [
                                    'level' => $level,
                                    'range' => $range,
                                    'date' => $nextDays->format('Y-m-d'),
                                    'existingClasses' => $existingClassesAmount,
                                    'create' => $classesToBeCreated
                                ];

                                $creatableClasses[] = $class;
                            }
                        }
                    }
                }
            }
        }

        $totalCreatedClasses = 0;
        $totalCalendarAddedClasses = 0;
        $totalTutorManagementSentClasses = 0;

        foreach ($creatableClasses as $key => $class) {
            $classStartTime = explode( '-', $class['range'])[0];
            $classEndTime = explode( '-', $class['range'])[1];
            $classStartTime = ($classStartTime == '00:00') ? '24:00' : $classStartTime;
            $classEndTime = ($classEndTime == '00:00') ? '24:00' : $classEndTime;

            $classDateTime = new DateTime($class['date'].' '.$classStartTime);
            $hourRange = explode(':', $classEndTime)[0] - explode(':', $classStartTime)[0];

            for ($i = 1; $i <= $class['create']; $i++) {
                $conversation = new Conversation();

                $conversation->level = $class['level'];
                $conversation->date = $classDateTime->format('Y-m-d');

                $minutes_to_add = 60;
                $randomStartTime = rand(0, $hourRange);
                $classDateTimeCopy = new DateTime($classDateTime->format('Y-m-d H:i'));
                $conversation->startsAt = $classDateTimeCopy->add(new DateInterval('PT' . ($minutes_to_add * $randomStartTime - (($randomStartTime == $hourRange) ? 60 : 0)) . 'M'))->format('H:i');
                $classDateTimeCopy->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                $conversation->endsAt = $classDateTimeCopy->format('H:i');

                $conversation->tutorId = 87;
                $conversation->visible = 'yes';

                $totalCreatedClasses++;


                /***________________________  API Call Start ________________________***/
                $topicByDate = Yii::$app->devSet->todayTopic($conversation->level, $conversation->date);
                $startDateTime = new DateTime($conversation->date . ' ' . $conversation->startsAt);
                $endsAtDateTime = new DateTime($conversation->date . ' ' . $conversation->startsAt);
                $endsAtDateTime->add(new DateInterval('PT' . $minutes_to_add . 'M'));

                if(!Yii::$app->devSet->isLocal()) {     // Google calendar data send
                    try {
                        $conversation->eventId = Yii::$app->googleCalendar->createEvent(
                            'DillBill Lesson',
                            'Moderator: Teacher will be assigned<br> Topic: ' . $topicByDate['description'] . ', ' . $topicByDate['type'],
                            'Asia/Baku',
                            date('c', strtotime(date('' . $startDateTime->format('Y-m-d H:i')))),
                            date('c', strtotime(date('' . $endsAtDateTime->format('Y-m-d H:i'))))
                        );

                        $totalCalendarAddedClasses++;
                    } catch (\Exception $exception) {}
                }

                $conversation->save(false);

                if(!Yii::$app->devSet->isLocal()) {    // TM data send
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
                            'Start_time: '.$startDateTime->format( 'm/d/Y g:i A'),
                            'End_time: '.$endsAtDateTime->format( 'm/d/Y g:i A'),
                            'Class_id: '.$conversation->id,
                            'Class_level: '.$conversation->level,
                            'Class_material: '.$topicByDate['url'],
                            'Class_topic: '.$topicByDate['description'],
                            'Authorization: Bearer '
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);

                    $totalTutorManagementSentClasses++;
                    //echo $response;
                }
                /***________________________  API Call End ________________________***/

            }
        }

        return [
            'totalCreatedClasses' => $totalCreatedClasses,
            'totalCalendarAddedClasses' => $totalCalendarAddedClasses,
            'totalTutorManagementSentClasses' => $totalTutorManagementSentClasses
        ];
    }

}