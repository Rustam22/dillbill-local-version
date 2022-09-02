<?php

use backend\models\Conversation;
use backend\models\ConversationUsers;
use backend\models\Feedback;
use common\models\UserParameters;
use yii\helpers\Url;

define("GROUP_SIZE", Yii::$app->devSet->getDevSet('conversationGroupSize'));

$userTimeZone = (Yii::$app->user->identity->userProfile->timezone == null) ? 'Asia/Baku' : Yii::$app->user->identity->userProfile->timezone;
$userDateTime = Yii::$app->devSet->getDateByTimeZone($userTimeZone);
$userSchedule = Yii::$app->user->identity->userParameters->currentSchedule;
$userAvailableTime = Yii::$app->user->identity->userParameters->availability;
$userLevel = Yii::$app->user->identity->userParameters->currentLevel;

$localDateTime = Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone());

$userStartTime = ($userAvailableTime != null) ? explode('-', $userAvailableTime)[0] : null;
$userEndTime = ($userAvailableTime != null) ? explode('-', $userAvailableTime)[1] : null;

$userStartTime = ($userStartTime == '00:00') ? '24:00' : $userStartTime;
$userEndTime = ($userEndTime == '00:00') ? '24:00' : $userEndTime;

//debug($userTimeZone);
//debug($userDateTime);

$anonymousTutor = 87;

$userLevel = Yii::$app->user->identity->userParameters->currentLevel;
$lang = Yii::$app->language;

$weekdays = [
    'en' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    'az' => ['Be', 'Ç.a', 'Çər', 'C.a', 'Cü', 'Şə'],
    'ru' => ['Пнд', 'Втр', 'Cрд', 'Чтв', 'Птн', 'Сбт'],
    'tr' => ['Pts', 'Sal', 'Çar', 'Per', 'Cum', 'Cts'],
    'pt' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
];

$availableTimes = [
    1 => [
        'start' => '09:00',
        'end' => '12:00',
        'startV' => '9:00',
        'endV' => '12:00'
    ],
    2 => [
        'start' => '15:00',
        'end' => '18:00',
        'startV' => '15:00',
        'endV' => '18:00'
    ],
    3 => [
        'start' => '18:00',
        'end' => '21:00',
        'startV' => '18:00',
        'endV' => '21:00'
    ],
    4 => [
        'start' => '21:00',
        'end' => '23:59',
        'startV' => '21:00',
        'endV' => '24:00'
    ]
];

$levels = [
    'beginner' =>           ['name' => 'Beginner', 'segment' => 'A1'],
    'elementary' =>         ['name' => 'Elementary', 'segment' => 'A12'],
    'pre-intermediate' =>   ['name' => 'Pre-Intermediate', 'segment' => 'B1.1'],
    'intermediate' =>       ['name' => 'Intermediate', 'segment' => 'B1.2'],
    'upper-intermediate' => ['name' => 'Upper-Intermediate', 'segment' => 'B2']
];

if (!isset($_COOKIE['class-tab'])) {
    $_COOKIE['class-tab'] = 'available-classes';
}

$fromDate = ($localDateTime->format('d') > $userDateTime->format('d')) ? $localDateTime : $userDateTime;
$classes = null;


/***------------------------ Empty Level ------------------------***/
$upcomingDates = null;

if(Yii::$app->user->identity->userParameters->currentLevel == 'empty') {
    $upcomingDates = Conversation::find()->
                                    select(['date'])->
                                    distinct()->
                                    where(['>=', 'date', $fromDate->format('Y-m-d')])->
                                    orderBy(['date' => SORT_ASC])->
                                    limit(6)->
                                    asArray()->
                                    all();
    //debug($upcomingDates);

    $upComingDate = ($upcomingDates == null) ? new DateTime() : new DateTime($upcomingDates[0]['date']);

    $bool = false;

    try {
        foreach ($upcomingDates as $key => $value)  {
            $datedDate = new DateTime($value['date']);
            if($datedDate->format('d.m.Y') == $_COOKIE['selected-date']) {
                $bool = true;
                break;
            }
        }
    } catch (Exception $exception) {

    }

    if(!$bool) {
        unset($_COOKIE['selected-date']);
    }

    if(!isset($_COOKIE['selected-date'])) {
        $_COOKIE['selected-date'] = $upComingDate->format('d.m.Y');
    }
}


/***------------------------ Non-Empty Level ------------------------***/
$availableDates = [];
$reservedClassExists = false;
$userStartDate = new DateTime(Yii::$app->user->identity->userParameters->startDate);

$fromDate = ($userStartDate >= $localDateTime) ? $userStartDate : $fromDate;

//debug(Yii::$app->devSet->adjustedDateTimeToSystemTimeZone($userDateTime, $userTimeZone));


if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') {
    $upcomingDates = Conversation::find()->
                                    select(['date'])->
                                    distinct()->
                                    where(['level' => Yii::$app->user->identity->userParameters->currentLevel])->
                                    andWhere(['>=', 'date', $fromDate->format('Y-m-d')])->
                                    orderBy(['date' => SORT_ASC])->
                                    limit(6)->
                                    asArray()->
                                    all();

    foreach ($upcomingDates as $key => $value) {
        $currentDate = new DateTime($value['date']);
        if (str_contains($userSchedule, $currentDate->format('w'))) {
            array_push($availableDates, $value['date']);
        }
    }

    $availableComingDate = ($availableDates == null) ? new DateTime() : new DateTime($availableDates[0]);

    $bool = false;

    try {
        foreach ($availableDates as $key => $value)  {
            $datedDate = new DateTime($value);
            if($datedDate->format('d.m.Y') == $_COOKIE['chosen-date']) {
                $bool = true;
                break;
            }
        }
    } catch (Exception $exception) {

    }

    if(!$bool) {
        unset($_COOKIE['chosen-date']);
    }

    if (!isset($_COOKIE['chosen-date'])) {
        $_COOKIE['chosen-date'] = $availableComingDate->format('d.m.Y');
    }

    if (!isset($_COOKIE['reservedClassExists'])) {
        $_COOKIE['reservedClassExists'] = false;
    }
}

?>

<link href="<?=Yii::getAlias('@web');?>/css/dashboard/class-card.css" rel="stylesheet">
<link href="<?=Yii::getAlias('@web');?>/css/dashboard/study-banner.css" rel="stylesheet">
<link href="<?=Yii::getAlias('@web');?>/css/dashboard/my-classes.css" rel="stylesheet">

<script>
    let _currentClassDate = '<?= (Yii::$app->user->identity->userParameters->currentLevel == 'empty') ? $_COOKIE['selected-date'] : $_COOKIE['chosen-date'] ?>'
    let _userDateTime = '<?= $userDateTime->format('M d, Y') ?>'
    let _userStartTime = '<?= $userStartTime ?>'
    let _userEndTime = '<?= $userEndTime ?>'
    let _startClassDate = false
    let _timeRange = false
    let _confirmStartDateAndTimeRangeUrl = '<?= Url::to(['dashboard/confirm-start-date'], true) ?>'
    let _feedbackConfirm = '<?= Url::to(['dashboard/feedback-confirm'], true) ?>'
</script>



<?php if(Yii::$app->user->identity->getCpBalance() <= 0) { ?>
<br>
<div class="welcome-back w-100">
    <div class="row">
        <div class="col-md-7 order-md-1 order-2">
            <h2 style="color: #646E82; font-weight: 400; font-size: 16px;">
                <?= Yii::$app->devSet->getTranslate('welcomeBack') ?>,
                <span style="color: #000000;font-weight: 600;"><?= Yii::$app->user->identity->userProfile->name ?>!</span> &nbsp;👋
            </h2>
            <h1 style="font-size: 24px;font-weight: 600;color:#000000;margin-top: 15px;">
                <?= Yii::$app->devSet->getTranslate('chooseYourPlanToGetStarted') ?>
            </h1>
            <p style="color: #000000;font-size: 16px;font-weight: 400;margin-top: 10px;margin-bottom: 25px;">
                <?= Yii::$app->devSet->getTranslate('beforeYouCanBook') ?>
            </p>
            <a href="<?= Url::to(['payment/index'], true) ?>">
                <button class="btn choose-your-plan">
                    <?= Yii::$app->devSet->getTranslate('chooseYourPlanButton') ?>
                </button>
            </a>
        </div>
        <div class="col-md-5 order-md-2 order-1" align="center">
            <img class="w-100" alt="online lesson on zoom" src="/img/dashboard/welcome-image.png">
        </div>
    </div>
</div>
<?php } ?>


<br>
<nav class="available-classes <?php if(Yii::$app->user->identity->getCpBalance() <= 0) { ?> display-none <?php } ?>">
    <div class="nav nav-tabs display-flex position-relative" id="nav-tab" role="tablist">
        <button class="nav-link <?php if($_COOKIE['class-tab'] == 'available-classes') { ?> active <?php } ?>"
                id="nav-home-tab"
                data-bs-toggle="tab"
                data-bs-target="#nav-home"
                type="button"
                role="tab"
                aria-controls="nav-home"
                aria-selected="true">
            <?= Yii::$app->devSet->getTranslate('availableClasses') ?>
        </button>

        <button class="nav-link display-flex <?php if($_COOKIE['class-tab'] == 'reserved-classes') { ?> active <?php } ?>"
                id="nav-profile-tab"
                data-bs-toggle="tab"
                data-bs-target="#nav-profile"
                type="button" role="tab"
                aria-controls="nav-profile"
                aria-selected="false">
            <?= Yii::$app->devSet->getTranslate('reservedClasses') ?>

            <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') { ?>
                <div class="red-dot display-none" style="margin-left: 8px; margin-top: 10px;"></div>
            <?php } ?>
        </button>
    </div>
</nav>

<div class="tab-content" id="nav-tabContent">

    <!------------------  Available Classes  -------------------->
    <div class="tab-pane fade <?php if($_COOKIE['class-tab'] == 'available-classes') { ?> show active <?php } ?>"
         id="nav-home"
         role="tabpanel"
         aria-labelledby="nav-home-tab">

        <!------------------  Time Date Selection -------------------->
        <?php if(Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
            <div class="time-date-select" style="display: none !important;">
                <div class="select-date dropdown" id="date-select">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton122" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="/img/dashboard/calendar-date.svg" alt="" style="margin-right: 0;">
                        &nbsp;<span class="selected-date">
                            <?= $_COOKIE['selected-date'] ?> - <?= $upComingDate->format('l') ?>
                        </span>&nbsp;
                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.3335 1L5.3335 5L1.3335 1" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton122">
                        <?php foreach ($upcomingDates as $key => $value) { ?>
                            <?php
                                $upComingDate = new DateTime($value['date'].' '.$localDateTime->format('H:i'));
                                $alignedUpComingDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($upComingDate, $userTimeZone)
                            ?>
                            <li>
                                <a class="dropdown-item <?php if($key == 0) { ?> active <?php } ?>" data-class-date="<?= $upComingDate->format('d.m.Y') ?>">
                                    <svg class="display-none" width="11" height="9" viewBox="0 0 11 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.28033 4.77513C0.987437 4.48223 0.512563 4.48223 0.21967 4.77513C-0.0732233 5.06802 -0.0732233 5.54289 0.21967 5.83579L2.94202 8.55814C3.23491 8.85103 3.70979 8.85103 4.00268 8.55814L10.447 2.11383C10.7399 1.82093 10.7399 1.34606 10.447 1.05317C10.1541 0.760273 9.67923 0.760273 9.38634 1.05317L3.47235 6.96715L1.28033 4.77513Z" fill="#1676F3"/>
                                    </svg>
                                    <text><?= $alignedUpComingDate->format('d.m.Y') ?> - <?= $alignedUpComingDate->format('l') ?></text>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>

                <?php
                    $rangeStart = ($userAvailableTime == null) ? Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$availableTimes[1]['start']), $userTimeZone)->format('H:i') : Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$userStartTime), $userTimeZone)->format('H:i');
                    $rangeEnd = ($userAvailableTime == null) ? Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$availableTimes[1]['end']), $userTimeZone)->format('H:i') : Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$userEndTime), $userTimeZone)->format('H:i');
                ?>
                <div class="select-date dropdown" id="time-select">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButtonTime" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                            <path d="M8.00016 14.6663C11.6821 14.6663 14.6668 11.6816 14.6668 7.99967C14.6668 4.31777 11.6821 1.33301 8.00016 1.33301C4.31826 1.33301 1.3335 4.31777 1.3335 7.99967C1.3335 11.6816 4.31826 14.6663 8.00016 14.6663Z" stroke="#1877F2" stroke-width="1.33" stroke-linejoin="round"/>
                            <path d="M8.00284 4L8.00244 8.00293L10.8289 10.8294" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        &nbsp;<span class="selected-time" data-time-start="<?= $availableTimes[1]['start'] ?>" data-time-end="<?= $availableTimes[1]['end'] ?>">
                            <?= $rangeStart ?> - <?= $rangeEnd ?>
                        </span>&nbsp;
                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.3335 1L5.3335 5L1.3335 1" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonTime">
                        <?php foreach ($availableTimes as $key => $value) {
                                $homeLandTimeStart = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$value['start']);
                                $homeLandTimeEnd = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$value['end']);
                                $userLandTimeStart = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($homeLandTimeStart, $userTimeZone);
                                $userLandTimeEnd = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($homeLandTimeEnd, $userTimeZone);
                            ?>
                            <?php if(($homeLandTimeStart->format('d') == $userLandTimeStart->format('d')) AND ($homeLandTimeEnd->format('d') == $userLandTimeEnd->format('d'))) { ?>
                                <li>
                                    <a data-start-time="<?= $value['start'] ?>"
                                       data-end-time="<?= $value['end'] ?>"
                                       class="dropdown-item time-dropdown
                                            <?php if($userStartTime == $value['start']) { ?> active <?php } ?>
                                            <?php if($key == 1 and $userAvailableTime == null) { ?> active <?php } ?>
                                            <?php if($value['start'] == '21:00') { ?> active <?php } ?>"
                                    >
                                        <svg class="display-none" width="11" height="9" viewBox="0 0 11 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.28033 4.77513C0.987437 4.48223 0.512563 4.48223 0.21967 4.77513C-0.0732233 5.06802 -0.0732233 5.54289 0.21967 5.83579L2.94202 8.55814C3.23491 8.85103 3.70979 8.85103 4.00268 8.55814L10.447 2.11383C10.7399 1.82093 10.7399 1.34606 10.447 1.05317C10.1541 0.760273 9.67923 0.760273 9.38634 1.05317L3.47235 6.96715L1.28033 4.77513Z" fill="#1676F3"/>
                                        </svg>
                                        <text>
                                            <?= $userLandTimeStart->format('H:i') ?> - <?= $userLandTimeEnd->format('H:i') ?>
                                        </text>
                                    </a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
            </div>


            <div class="today-topic display-flex position-relative">
                <div class="red-mark position-absolute">
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0V18L8 14.1561L16 18V0H0Z" fill="#FF3838"/>
                    </svg>
                </div>

                <div class="topic-side display-flex">
                    <div class="topic-group" data-topic-date="28.04.2022" style="">
                        <div>
                            <img class="" src="/img/dashboard/reading-day.svg" alt="" width="48">
                            <img class=" display-none " src="/img/dashboard/grammar-day.svg" alt="" width="48">
                        </div>
                        <div style="margin-top: -8px;">
                            <span>
                                SPEAKING TOPIC &nbsp;
                            </span>
                            <h5>Body language</h5>
                        </div>
                    </div>
                </div>

                <div class="buttons-side display-flex">
                    <div class="topic-group" data-topic-date="28.04.2022" style="">
                        <a href="https://drive.google.com/file/d/1UQKdMVKHZnOmzPwF8VIHeCZtN-zXjBkx/view?usp=sharing	" class="w-100" target="_blank">
                            <div class="btn download-material">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"></path>
                                    <path d="M2 8.00277V14H14V8" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M11 7.6665L8 10.6665L5 7.6665" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M7.99707 2V10.6667" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                &nbsp;Download
                            </div>
                        </a>
                    </div>

                    <div class="select-date dropdown" id="date-select">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton122" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/img/dashboard/calendar-date.svg" alt="" style="margin-right: 0;">
                            &nbsp;<span class="selected-date">
                            <?= $_COOKIE['selected-date'] ?> - <?= $upComingDate->format('l') ?>
                        </span>&nbsp;
                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.3335 1L5.3335 5L1.3335 1" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton122">
                            <?php foreach ($upcomingDates as $key => $value) { ?>
                                <?php
                                $upComingDate = new DateTime($value['date'].' '.$localDateTime->format('H:i'));
                                $alignedUpComingDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($upComingDate, $userTimeZone)
                                ?>
                                <li>
                                    <a class="dropdown-item <?php if($key == 0) { ?> active <?php } ?>" data-class-date="<?= $upComingDate->format('d.m.Y') ?>">
                                        <svg class="display-none" width="11" height="9" viewBox="0 0 11 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.28033 4.77513C0.987437 4.48223 0.512563 4.48223 0.21967 4.77513C-0.0732233 5.06802 -0.0732233 5.54289 0.21967 5.83579L2.94202 8.55814C3.23491 8.85103 3.70979 8.85103 4.00268 8.55814L10.447 2.11383C10.7399 1.82093 10.7399 1.34606 10.447 1.05317C10.1541 0.760273 9.67923 0.760273 9.38634 1.05317L3.47235 6.96715L1.28033 4.77513Z" fill="#1676F3"/>
                                        </svg>
                                        <text><?= $alignedUpComingDate->format('d.m.Y') ?> - <?= $alignedUpComingDate->format('l') ?></text>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

            </div>
        <?php } ?>


        <!-------------------- Class Cards For Empty Users -------------------->
        <?php if(Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
            <div class="testimonial-classes display-none">
                <div class="classes-row row justify-content-lg-start justify-content-md-around">
                    <?php foreach ($upcomingDates as $date) { ?>
                        <?php foreach ($availableTimes as $key => $time) { ?>
                            <?php $classes = Conversation::find()->
                                                            where(['date' => $date['date']])->
                                                            andWhere(['>=', 'startsAt', $time['start']])->
                                                            andWhere(['<', 'startsAt', $time['end']])->
                                                            limit(3)->
                                                            //asArray()->
                                                            all(); ?>
                            <?php //debug(sizeof($classes)); ?>
                            <?php foreach ($classes as $class) { ?>
                                <?php //debug($class->getConversationUsers()->asArray()->all()); ?>
                                <?php
                                    $classDate = new DateTime($class->date);
                                    $classAlignedDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$localDateTime->format('H:i')), $userTimeZone);
                                    $classAlignedStartDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$class->startsAt), $userTimeZone);
                                    $classAlignedEndDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$class->endsAt), $userTimeZone);
                                ?>
                                <?php if($class->tutorId != null) { ?>
                                    <div class="class-column col-lg-4 col-auto"
                                         data-class-date="<?= $classDate->format('d.m.Y') ?>"
                                         data-start-time="<?= $class->startsAt ?>"
                                         data-end-time="<?= $class->endsAt ?>">
                                        <div class="class-card">
                                            <section class="tutor-section">
                                                <div class="display-flex">
                                                    <div class="story <?php if($class->teacher->presentation != null) { ?> clickable-story <?php } ?>"
                                                         data-name="<?php if($class->teacher->teacherName != null) { ?><?= $class->teacher->teacherName ?><?php } ?>"
                                                         data-country="<?php if($class->teacher->country != null) { ?><?= $class->teacher->country ?><?php } ?>"
                                                         data-experience="<?php if($class->teacher->experience != null) { ?><?= $class->teacher->experience ?><?php } ?>"
                                                         data-description="<?php if($class->teacher['description_'.Yii::$app->language] != null) { ?><?= $class->teacher['description_'.Yii::$app->language] ?><?php } ?>"
                                                         data-presentation="<?php if($class->teacher->presentation != null) { ?><?= $class->teacher->presentation ?><?php } ?>"
                                                        <?php if($class->teacher->presentation != null) { ?>
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#exampleModal5"
                                                        <?php } ?>
                                                    >
                                                        <img class="tutor-image" src="<?= Yii::$app->request->hostInfo ?>/backend/web/<?= $class->teacher->image ?>" alt="">
                                                    </div>
                                                    <div class="t-info">
                                                        <?php if($class->tutorId == $anonymousTutor) { ?>
                                                            <div class="display-flex h-100 align-items-center">
                                                                <h6 style="font-weight: 600;font-size: 15px;line-height: 16px;">
                                                                    <?= Yii::$app->devSet->getTranslate('teacherWillBeAssigned') ?>
                                                                </h6>
                                                            </div>
                                                        <?php } else { ?>
                                                            <span><?= Yii::$app->devSet->getTranslate('tutor') ?></span>
                                                            <h5><?= $class->teacher->teacherName ?></h5>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </section>

                                            <div class="separator w-100"></div>

                                            <section class="informative-section display-flex">
                                                <div class="date-time">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.99967 14.6667H13.9997C14.1765 14.6667 14.3461 14.5964 14.4711 14.4714C14.5961 14.3464 14.6663 14.1768 14.6663 14V4C14.6663 3.82319 14.5961 3.65362 14.4711 3.5286C14.3461 3.40357 14.1765 3.33334 13.9997 3.33334H11.333V2C11.333 1.82319 11.2628 1.65362 11.1377 1.5286C11.0127 1.40357 10.8432 1.33334 10.6663 1.33334C10.4895 1.33334 10.32 1.40357 10.1949 1.5286C10.0699 1.65362 9.99967 1.82319 9.99967 2V3.33334H5.99967V2C5.99967 1.82319 5.92944 1.65362 5.80441 1.5286C5.67939 1.40357 5.50982 1.33334 5.33301 1.33334C5.1562 1.33334 4.98663 1.40357 4.8616 1.5286C4.73658 1.65362 4.66634 1.82319 4.66634 2V3.33334H1.99967C1.82286 3.33334 1.65329 3.40357 1.52827 3.5286C1.40325 3.65362 1.33301 3.82319 1.33301 4V14C1.33301 14.1768 1.40325 14.3464 1.52827 14.4714C1.65329 14.5964 1.82286 14.6667 1.99967 14.6667ZM2.66634 4.66667H13.333V6.66667H2.66634V4.66667ZM2.66634 8H13.333V13.3333H2.66634V8Z" fill="#848FA3"/>
                                                </svg>
                                                &nbsp;<?= Yii::$app->devSet->getTranslate('date') ?>
                                            </span>
                                                    <p><?= $classAlignedDate->format('M d, Y') ?></p>
                                                </div>
                                                <div class="date-time">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.00033 0.666664C6.54993 0.666664 5.13211 1.09676 3.92615 1.90255C2.72019 2.70835 1.78025 3.85366 1.22521 5.19365C0.67017 6.53364 0.524945 8.00813 0.807903 9.43066C1.09086 10.8532 1.78929 12.1599 2.81488 13.1854C3.84046 14.211 5.14714 14.9095 6.56967 15.1924C7.99219 15.4754 9.46668 15.3302 10.8067 14.7751C12.1467 14.2201 13.292 13.2801 14.0978 12.0742C14.9036 10.8682 15.3337 9.45039 15.3337 8C15.3314 6.05578 14.558 4.19185 13.1832 2.81708C11.8085 1.44231 9.94455 0.668958 8.00033 0.666664ZM8.00033 14C6.81364 14 5.6536 13.6481 4.66691 12.9888C3.68021 12.3295 2.91118 11.3925 2.45705 10.2961C2.00293 9.19974 1.88411 7.99334 2.11562 6.82946C2.34713 5.66557 2.91857 4.59647 3.75769 3.75736C4.5968 2.91824 5.6659 2.3468 6.82979 2.11529C7.99367 1.88377 9.20007 2.00259 10.2964 2.45672C11.3928 2.91085 12.3299 3.67988 12.9891 4.66658C13.6484 5.65327 14.0003 6.81331 14.0003 8C13.9986 9.59075 13.3659 11.1159 12.241 12.2407C11.1162 13.3655 9.59109 13.9982 8.00033 14ZM12.0003 8C12.0003 8.17681 11.9301 8.34638 11.8051 8.4714C11.68 8.59643 11.5105 8.66666 11.3337 8.66666H8.00033C7.82352 8.66666 7.65395 8.59643 7.52892 8.4714C7.4039 8.34638 7.33366 8.17681 7.33366 8V4C7.33366 3.82319 7.4039 3.65362 7.52892 3.52859C7.65395 3.40357 7.82352 3.33333 8.00033 3.33333C8.17714 3.33333 8.34671 3.40357 8.47173 3.52859C8.59676 3.65362 8.667 3.82319 8.667 4V7.33333H11.3337C11.5105 7.33333 11.68 7.40357 11.8051 7.52859C11.9301 7.65362 12.0003 7.82319 12.0003 8Z" fill="#848FA3"/>
                                                </svg>
                                                &nbsp;<?= Yii::$app->devSet->getTranslate('time') ?>
                                            </span>
                                                    <p><?= $classAlignedStartDateTime->format('H:i') ?> - <?= $classAlignedEndDateTime->format('H:i') ?></p>
                                                </div>
                                            </section>

                                            <section class="attendees">
                                                <div class="display-flex">
                                                    <?php $counter = 0; ?>
                                                    <?php foreach($class->getConversationUsers()->all() as $conversationUser) { ?>
                                                        <?php
                                                            $counter++;
                                                            $colors = [1 => '#FBBC04', 2 => '#35C9D4', 3 => '#DD33AC', 4 => '#00B67A', 5 => '#EE6011', 6 => '#1877F2', 7 => '#763BE0', 8 => '#2B82ED'];
                                                            $randColor = array_rand($colors);
                                                            $color = $colors[$randColor];
                                                        ?>
                                                        <?php if($counter <= GROUP_SIZE) { ?>
                                                            <div class="attendee" style="background-color: <?php if($conversationUser->user->userProfile->color == null) { ?> <?= $color ?> <?php } else { ?> <?= $conversationUser->user->userProfile->color ?> <?php } ?>; z-index: 4;">
                                                                <?= strtoupper(substr($conversationUser->user->username, 0,1)) ?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <?php for($counter += 1; $counter <= GROUP_SIZE; $counter++) { ?>
                                                        <div class="waiting-attendee" style="z-index: 2;">
                                                            <img class="tutor-image" src="<?=Yii::getAlias('@web');?>/img/dashboard/add-profile.svg" alt="">
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </section>

                                            <section class="action-section">
                                                <div class="btn reserve-room-button">
                                                    <?= Yii::$app->devSet->getTranslate('reserve') ?>
                                                    <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>


        <!-------------------- Study Banner -------------------->
        <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') { ?>
            <div class="today-topic display-flex position-relative ">
                <div class="red-mark position-absolute">
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0V18L8 14.1561L16 18V0H0Z" fill="#FF3838"/>
                    </svg>
                </div>

                <div class="select-date dropdown position-relative" id="date-select">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton666" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="/img/dashboard/calendar-date.svg" alt="" style="margin-right: 0;">
                        <?php $weekday = new DateTime($_COOKIE['chosen-date']); ?>
                        &nbsp;<span class="selected-date"><?= $_COOKIE['chosen-date'] ?> - <?= $weekday->format('l') ?></span>&nbsp;
                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.3335 1L5.3335 5L1.3335 1" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton666">
                        <?php foreach ($availableDates as $key => $value) { ?>
                            <?php
                            $availableDate = new DateTime($value.' '.$localDateTime->format('H:i'));
                            $alignedAvailableDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($availableDate, $userTimeZone);
                            ?>
                            <li>
                                <a class="dropdown-item <?php if($key == 0) { ?> active <?php } ?>" data-class-date="<?= $availableDate->format('d.m.Y') ?>">
                                    <svg class="display-none" width="11" height="9" viewBox="0 0 11 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.28033 4.77513C0.987437 4.48223 0.512563 4.48223 0.21967 4.77513C-0.0732233 5.06802 -0.0732233 5.54289 0.21967 5.83579L2.94202 8.55814C3.23491 8.85103 3.70979 8.85103 4.00268 8.55814L10.447 2.11383C10.7399 1.82093 10.7399 1.34606 10.447 1.05317C10.1541 0.760273 9.67923 0.760273 9.38634 1.05317L3.47235 6.96715L1.28033 4.77513Z" fill="#1676F3"/>
                                    </svg>
                                    <text><?= $alignedAvailableDate->format('d.m.Y') ?> - <?= $alignedAvailableDate->format('l') ?></text>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                    <div class="separator-line position-absolute"></div>
                </div>

                <div class="topic-side display-flex">
                    <?php foreach ($availableDates as $key => $value) {  ?>
                        <?php
                            $availableDate = new DateTime($value);
                            $topicByDate = Yii::$app->devSet->todayTopic($userLevel, $value);
                        ?>
                        <div class="topic-group <?php if($availableDate->format('d.m.Y') != $_COOKIE['chosen-date']) { ?> display-none-important <?php } ?>"
                             data-topic-date="<?= $availableDate->format('d.m.Y') ?>">
                            <div>
                                <img class="" width="48" src="<?=Yii::getAlias('@web');?>/img/dashboard/reading-day.svg" alt="">
                            </div>
                            <div style="margin-top: -8px;">
                                <span>
                                    <?= Yii::$app->devSet->getTranslate('speakingTopic') ?>,
                                    &nbsp;
                                    <?= $levels[Yii::$app->user->identity->userParameters->currentLevel]['segment'] ?> - <b style="color: #00B67A;"><?= $levels[Yii::$app->user->identity->userParameters->currentLevel]['name'] ?></b>
                                </span>
                                <h5>
                                    <?= $topicByDate['description'] ?>
                                </h5>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="buttons-side display-flex">
                    <?php foreach ($availableDates as $key => $value) {  ?>
                        <?php
                            $availableDate = new DateTime($value);
                            $topicByDate = Yii::$app->devSet->todayTopic($userLevel, $value);
                        ?>
                        <div class="topic-group <?php if($availableDate->format('d.m.Y') != $_COOKIE['chosen-date']) { ?> display-none-important <?php } ?>"
                             data-topic-date="<?= $availableDate->format('d.m.Y') ?>">
                            <a href="<?= $topicByDate['url'] ?>" class="w-100" target="_blank">
                                <div class="btn download-material">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                        <path d="M2 8.00277V14H14V8" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M11 7.6665L8 10.6665L5 7.6665" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7.99707 2V10.6667" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    &nbsp;<?= Yii::$app->devSet->getTranslate('download') ?>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>


        <!-------------------- Class Cards -------------------->
        <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') { ?>
            <div class="testimonial-classes">
                <div class="classes-row row justify-content-lg-start justify-content-md-around">
                    <?php
                        if (Yii::$app->user->identity->userParameters->availability == '' OR Yii::$app->user->identity->userParameters->availability == null) {
                            $classes = Conversation::find()->
                            where(['date' => $availableDates, 'level' => $userLevel])->
                            orderBy(['date' => SORT_ASC, 'startsAt' => SORT_ASC])->
                            all();
                        } else {
                            $classes = Conversation::find()->
                            where(['date' => $availableDates, 'level' => $userLevel])->
                            andWhere(['>=', 'startsAt', $userStartTime])->
                            andWhere(['<', 'startsAt', $userEndTime])->
                            orderBy(['date' => SORT_ASC, 'startsAt' => SORT_ASC])->
                            all();
                        }
                    ?>
                    <?php foreach ($classes as $class) { ?>
                        <?php
                            $aboutClass = '';
                            $conversationUsers = $class->getConversationUsers()->all();

                            foreach ($conversationUsers as $conversationUser) {
                                if($conversationUser->action == 'reserve' AND $conversationUser->user->id == Yii::$app->user->id) {
                                    $aboutClass = 'reserved';
                                    break;
                                }
                            }
                        ?>

                        <?php /*if($aboutClass != 'reserved') { */?>
                            <?php
                                $classDate = new DateTime($class->date.' '.$localDateTime->format('H:i'));
                                $classAlignedDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$class->startsAt), $userTimeZone);
                                $classAlignedStartDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$class->startsAt), $userTimeZone);
                                $classAlignedEndDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$class->endsAt), $userTimeZone);

                            ?>
                            <?php if($class->tutorId != null) { ?>
                                <div class="class-column col-lg-4 col-auto <?= $aboutClass ?>"
                                     data-class-id="<?= $class->id ?>"
                                     data-class-date="<?= $classDate->format('d.m.Y') ?>"
                                     data-start-time="<?= $class->startsAt ?>"
                                     data-end-time="<?= $class->endsAt ?>"
                                     data-class-start-date-time="<?= $classDate->format('M d, Y '.$class->startsAt) ?>"
                                     data-class-end-date-time="<?= $classDate->format('M d, Y '.$class->endsAt) ?>"

                                    <?php if($classDate->format('d.m.Y') != $_COOKIE['chosen-date']) { ?> style="display: none;" <?php } ?>
                                >
                                    <div class="class-card">
                                        <section class="tutor-section">
                                            <div class="display-flex">
                                                <div class="story <?php if($class->teacher->presentation != null) { ?> clickable-story <?php } ?>"
                                                     data-name="<?php if($class->teacher->teacherName != null) { ?><?= $class->teacher->teacherName ?><?php } ?>"
                                                     data-country="<?php if($class->teacher->country != null) { ?><?= $class->teacher->country ?><?php } ?>"
                                                     data-experience="<?php if($class->teacher->experience != null) { ?><?= $class->teacher->experience ?><?php } ?>"
                                                     data-description="<?php if($class->teacher['description_'.Yii::$app->language] != null) { ?><?= $class->teacher['description_'.Yii::$app->language] ?><?php } ?>"
                                                     data-presentation="<?php if($class->teacher->presentation != null) { ?><?= $class->teacher->presentation ?><?php } ?>"
                                                    <?php if($class->teacher->presentation != null) { ?>
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal5"
                                                    <?php } ?>
                                                >
                                                    <img class="tutor-image" src="<?= Yii::$app->request->hostInfo ?>/backend/web/<?= $class->teacher->image ?>" alt="">
                                                </div>
                                                <div class="t-info">
                                                    <?php if($class->tutorId == $anonymousTutor) { ?>
                                                        <div class="display-flex h-100 align-items-center">
                                                            <h6 style="font-weight: 600;font-size: 15px;line-height: 16px;">
                                                                <?= Yii::$app->devSet->getTranslate('teacherWillBeAssigned') ?>
                                                            </h6>
                                                        </div>
                                                    <?php } else { ?>
                                                        <span><?= Yii::$app->devSet->getTranslate('tutor') ?></span>
                                                        <h5><?= $class->teacher->teacherName ?></h5>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </section>

                                        <div class="separator w-100"></div>

                                        <section class="informative-section display-flex">
                                            <div class="date-time">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.99967 14.6667H13.9997C14.1765 14.6667 14.3461 14.5964 14.4711 14.4714C14.5961 14.3464 14.6663 14.1768 14.6663 14V4C14.6663 3.82319 14.5961 3.65362 14.4711 3.5286C14.3461 3.40357 14.1765 3.33334 13.9997 3.33334H11.333V2C11.333 1.82319 11.2628 1.65362 11.1377 1.5286C11.0127 1.40357 10.8432 1.33334 10.6663 1.33334C10.4895 1.33334 10.32 1.40357 10.1949 1.5286C10.0699 1.65362 9.99967 1.82319 9.99967 2V3.33334H5.99967V2C5.99967 1.82319 5.92944 1.65362 5.80441 1.5286C5.67939 1.40357 5.50982 1.33334 5.33301 1.33334C5.1562 1.33334 4.98663 1.40357 4.8616 1.5286C4.73658 1.65362 4.66634 1.82319 4.66634 2V3.33334H1.99967C1.82286 3.33334 1.65329 3.40357 1.52827 3.5286C1.40325 3.65362 1.33301 3.82319 1.33301 4V14C1.33301 14.1768 1.40325 14.3464 1.52827 14.4714C1.65329 14.5964 1.82286 14.6667 1.99967 14.6667ZM2.66634 4.66667H13.333V6.66667H2.66634V4.66667ZM2.66634 8H13.333V13.3333H2.66634V8Z" fill="#848FA3"/>
                                                </svg>
                                                &nbsp;<?= Yii::$app->devSet->getTranslate('date') ?>
                                            </span>
                                                <p><?= $classAlignedDate->format('F d') ?> &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;</p>
                                            </div>
                                            <div class="date-time">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.00033 0.666664C6.54993 0.666664 5.13211 1.09676 3.92615 1.90255C2.72019 2.70835 1.78025 3.85366 1.22521 5.19365C0.67017 6.53364 0.524945 8.00813 0.807903 9.43066C1.09086 10.8532 1.78929 12.1599 2.81488 13.1854C3.84046 14.211 5.14714 14.9095 6.56967 15.1924C7.99219 15.4754 9.46668 15.3302 10.8067 14.7751C12.1467 14.2201 13.292 13.2801 14.0978 12.0742C14.9036 10.8682 15.3337 9.45039 15.3337 8C15.3314 6.05578 14.558 4.19185 13.1832 2.81708C11.8085 1.44231 9.94455 0.668958 8.00033 0.666664ZM8.00033 14C6.81364 14 5.6536 13.6481 4.66691 12.9888C3.68021 12.3295 2.91118 11.3925 2.45705 10.2961C2.00293 9.19974 1.88411 7.99334 2.11562 6.82946C2.34713 5.66557 2.91857 4.59647 3.75769 3.75736C4.5968 2.91824 5.6659 2.3468 6.82979 2.11529C7.99367 1.88377 9.20007 2.00259 10.2964 2.45672C11.3928 2.91085 12.3299 3.67988 12.9891 4.66658C13.6484 5.65327 14.0003 6.81331 14.0003 8C13.9986 9.59075 13.3659 11.1159 12.241 12.2407C11.1162 13.3655 9.59109 13.9982 8.00033 14ZM12.0003 8C12.0003 8.17681 11.9301 8.34638 11.8051 8.4714C11.68 8.59643 11.5105 8.66666 11.3337 8.66666H8.00033C7.82352 8.66666 7.65395 8.59643 7.52892 8.4714C7.4039 8.34638 7.33366 8.17681 7.33366 8V4C7.33366 3.82319 7.4039 3.65362 7.52892 3.52859C7.65395 3.40357 7.82352 3.33333 8.00033 3.33333C8.17714 3.33333 8.34671 3.40357 8.47173 3.52859C8.59676 3.65362 8.667 3.82319 8.667 4V7.33333H11.3337C11.5105 7.33333 11.68 7.40357 11.8051 7.52859C11.9301 7.65362 12.0003 7.82319 12.0003 8Z" fill="#848FA3"/>
                                                </svg>
                                                &nbsp;<?= Yii::$app->devSet->getTranslate('time') ?>
                                            </span>
                                                <p><?= $classAlignedStartDateTime->format('H:i') ?> - <?= $classAlignedEndDateTime->format('H:i') ?></p>
                                            </div>
                                        </section>

                                        <section class="attendees">
                                            <div class="display-flex">
                                                <?php $counter = 0; ?>
                                                <?php foreach($conversationUsers as $conversationUser) { ?>
                                                    <?php if($conversationUser->action == 'reserve') { ?>
                                                        <?php
                                                            $counter++;
                                                            $colors = [1 => '#FBBC04', 2 => '#35C9D4', 3 => '#DD33AC', 4 => '#00B67A', 5 => '#EE6011', 6 => '#1877F2', 7 => '#763BE0', 8 => '#2B82ED'];
                                                            $randColor = shuffle($colors);
                                                            $color = $colors[$randColor];
                                                        ?>
                                                        <?php if($counter <= GROUP_SIZE) { ?>
                                                            <div class="attendee" data-user-id="<?= $conversationUser->user->id ?>" style="background-color: <?php if($conversationUser->user->userProfile->color == null) { ?> <?= $color ?> <?php } else { ?> <?= $conversationUser->user->userProfile->color ?> <?php } ?>;">
                                                                <?= strtoupper(substr($conversationUser->user->username, 0,1)) ?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php for($counter += 1; $counter <= GROUP_SIZE; $counter++) { ?>
                                                    <div class="waiting-attendee">
                                                        <img src="<?=Yii::getAlias('@web');?>/img/dashboard/add-profile.svg" alt="">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </section>

                                        <section class="action-section">
                                            <section class="reserve-class <?php if($aboutClass == 'reserved') { ?> display-none <?php } ?>">
                                                <div class="btn reserve-room-button">
                                                    <?= Yii::$app->devSet->getTranslate('reserve') ?>
                                                    <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                                </div>
                                            </section>

                                            <section class="enter-room-section <?php if($aboutClass != 'reserved') { ?> display-none <?php } ?>">
                                                <div class="display-flex">
                                                    <div class="btn enter-room-button">
                                                        <?= Yii::$app->devSet->getTranslate('enterRoom') ?>
                                                        <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                                    </div>
                                                    <div class="btn cancel-room-button position-relative">
                                                        <svg class="position-absolute" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5733 5.01755C14.7866 4.80428 14.7863 4.44504 14.5646 4.22331L13.7765 3.43527C13.5453 3.20406 13.1992 3.20964 12.9823 3.42656L8.99984 7.40902L5.01738 3.42656C4.80411 3.21329 4.44487 3.21354 4.22314 3.43527L3.4351 4.22331C3.20389 4.45452 3.20947 4.80063 3.42639 5.01755L7.40885 9.00001L3.42639 12.9825C3.21312 13.1957 3.21337 13.555 3.4351 13.7767L4.22314 14.5647C4.45435 14.796 4.80046 14.7904 5.01738 14.5735L8.99984 10.591L12.9823 14.5735C13.1956 14.7867 13.5548 14.7865 13.7765 14.5647L14.5646 13.7767C14.7958 13.5455 14.7902 13.1994 14.5733 12.9825L10.5908 9.00001L14.5733 5.01755Z" fill="#848FA3"/>
                                                        </svg>
                                                        <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true" style="margin: 0;"></span>
                                                    </div>
                                                </div>
                                                <div class="tik-tok w-100">
                                                    <div class="display-flex" style="justify-content: center;height: 100%">
                                                        <div class="counter">
                                                            <b class="hours">00</b>
                                                            <p><?= Yii::$app->devSet->getTranslate('hours') ?></p>
                                                        </div>
                                                        <div class="counter">
                                                            <b class="minutes">00</b>
                                                            <p><?= Yii::$app->devSet->getTranslate('minutes') ?></p>
                                                        </div>
                                                        <div class="counter">
                                                            <b class="seconds">00</b>
                                                            <p><?= Yii::$app->devSet->getTranslate('seconds') ?></p>
                                                        </div>
                                                    </div>
                                            </section>
                                        </section>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php /*} */?>

                    <?php } ?>
                </div>
            </div>
        <?php } ?>


        <!-------------------- No Class -------------------->
        <div class="no-class row display-none">
            <div class="col-12" align="center">
                <div class="no-classes">
                    <img width="107" src="<?=Yii::getAlias('@web');?>/img/dashboard/emptyClass.svg" alt="">
                    <h5>
                        <?= Yii::$app->devSet->getTranslate('noAvailableClasses') ?>
                    </h5>
                    <p>
                        <?= Yii::$app->devSet->getTranslate('requestNewClassContact') ?>
                    </p>

                    <button class="btn g-to-classes live-chat">
                        <?= Yii::$app->devSet->getTranslate('contactSupport') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!------------------  Reserved Classes  -------------------->
    <div class="tab-pane fade <?php if($_COOKIE['class-tab'] == 'reserved-classes') { ?> show active <?php } ?>"
         id="nav-profile"
         role="tabpanel"
         aria-labelledby="nav-profile-tab">
        <br>

        <!-------------------- Reserved Classes -------------------->
        <div class="testimonial-classes">
            <div class="classes-row row justify-content-lg-start justify-content-md-around">
                <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') { ?>
                    <?php
                        $classes = Conversation::find()->
                                                where(['date' => $availableDates, 'level' => $userLevel])->
                                                andWhere(['>=', 'startsAt', $userStartTime])->
                                                andWhere(['<=', 'startsAt', $userEndTime])->
                                                orderBy(['date' => SORT_ASC, 'startsAt' => SORT_ASC])->
                                                all();
                    ?>
                    <?php foreach ($classes as $class) { ?>
                        <?php
                            $aboutClass = '';
                            $conversationUsers = $class->getConversationUsers()->all();

                            foreach ($conversationUsers as $conversationUser) {
                                if($conversationUser->action == 'reserve' AND $conversationUser->user->id == Yii::$app->user->id) {
                                    $aboutClass = 'reserved';
                                    $reservedClassExists = true;
                                    break;
                                }
                            }
                        ?>

                        <?php if($aboutClass == 'reserved') { ?>
                            <?php
                                $classDate = new DateTime($class->date);
                                $classAlignedDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date), $userTimeZone);
                                $classAlignedStartDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.''.$class->startsAt), $userTimeZone);
                                $classAlignedEndDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.''.$class->endsAt), $userTimeZone);
                            ?>
                            <?php if($class->tutorId != null) { ?>
                                <div class="class-column col-lg-4 col-auto <?= $aboutClass ?>"
                                     data-class-id="<?= $class->id ?>"
                                     data-class-date="<?= $classDate->format('d.m.Y') ?>"
                                     data-start-time="<?= $class->startsAt ?>"
                                     data-end-time="<?= $class->endsAt ?>"
                                     data-class-start-date-time="<?= $classDate->format('M d, Y '.$class->startsAt) ?>"
                                     data-class-end-date-time="<?= $classDate->format('M d, Y '.$class->endsAt) ?>"
                                >
                                    <div class="class-card">
                                        <section class="tutor-section">
                                            <div class="display-flex">
                                                <div class="story <?php if($class->teacher->presentation != null) { ?> clickable-story <?php } ?>"
                                                     data-name="<?php if($class->teacher->teacherName != null) { ?><?= $class->teacher->teacherName ?><?php } ?>"
                                                     data-country="<?php if($class->teacher->country != null) { ?><?= $class->teacher->country ?><?php } ?>"
                                                     data-experience="<?php if($class->teacher->experience != null) { ?><?= $class->teacher->experience ?><?php } ?>"
                                                     data-description="<?php if($class->teacher['description_'.Yii::$app->language] != null) { ?><?= $class->teacher['description_'.Yii::$app->language] ?><?php } ?>"
                                                     data-presentation="<?php if($class->teacher->presentation != null) { ?><?= $class->teacher->presentation ?><?php } ?>"
                                                    <?php if($class->teacher->presentation != null) { ?>
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal5"
                                                    <?php } ?>
                                                >
                                                    <img class="tutor-image" src="<?= Yii::$app->request->hostInfo ?>/backend/web/<?= $class->teacher->image ?>" alt="">
                                                </div>
                                                <div class="t-info">
                                                    <?php if($class->tutorId == $anonymousTutor) { ?>
                                                        <div class="display-flex h-100 align-items-center">
                                                            <h6 style="font-weight: 600;font-size: 15px;line-height: 16px;">
                                                                <?= Yii::$app->devSet->getTranslate('teacherWillBeAssigned') ?>
                                                            </h6>
                                                        </div>
                                                    <?php } else { ?>
                                                        <span><?= Yii::$app->devSet->getTranslate('tutor') ?></span>
                                                        <h5><?= $class->teacher->teacherName ?></h5>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </section>

                                        <div class="separator w-100"></div>

                                        <section class="informative-section display-flex">
                                            <div class="date-time">
                                                    <span>
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1.99967 14.6667H13.9997C14.1765 14.6667 14.3461 14.5964 14.4711 14.4714C14.5961 14.3464 14.6663 14.1768 14.6663 14V4C14.6663 3.82319 14.5961 3.65362 14.4711 3.5286C14.3461 3.40357 14.1765 3.33334 13.9997 3.33334H11.333V2C11.333 1.82319 11.2628 1.65362 11.1377 1.5286C11.0127 1.40357 10.8432 1.33334 10.6663 1.33334C10.4895 1.33334 10.32 1.40357 10.1949 1.5286C10.0699 1.65362 9.99967 1.82319 9.99967 2V3.33334H5.99967V2C5.99967 1.82319 5.92944 1.65362 5.80441 1.5286C5.67939 1.40357 5.50982 1.33334 5.33301 1.33334C5.1562 1.33334 4.98663 1.40357 4.8616 1.5286C4.73658 1.65362 4.66634 1.82319 4.66634 2V3.33334H1.99967C1.82286 3.33334 1.65329 3.40357 1.52827 3.5286C1.40325 3.65362 1.33301 3.82319 1.33301 4V14C1.33301 14.1768 1.40325 14.3464 1.52827 14.4714C1.65329 14.5964 1.82286 14.6667 1.99967 14.6667ZM2.66634 4.66667H13.333V6.66667H2.66634V4.66667ZM2.66634 8H13.333V13.3333H2.66634V8Z" fill="#848FA3"/>
                                                        </svg>
                                                        &nbsp;<?= Yii::$app->devSet->getTranslate('date') ?>
                                                    </span>
                                                <p><?= $classAlignedDate->format('F d') ?> &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;</p>
                                            </div>
                                            <div class="date-time">
                                                    <span>
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8.00033 0.666664C6.54993 0.666664 5.13211 1.09676 3.92615 1.90255C2.72019 2.70835 1.78025 3.85366 1.22521 5.19365C0.67017 6.53364 0.524945 8.00813 0.807903 9.43066C1.09086 10.8532 1.78929 12.1599 2.81488 13.1854C3.84046 14.211 5.14714 14.9095 6.56967 15.1924C7.99219 15.4754 9.46668 15.3302 10.8067 14.7751C12.1467 14.2201 13.292 13.2801 14.0978 12.0742C14.9036 10.8682 15.3337 9.45039 15.3337 8C15.3314 6.05578 14.558 4.19185 13.1832 2.81708C11.8085 1.44231 9.94455 0.668958 8.00033 0.666664ZM8.00033 14C6.81364 14 5.6536 13.6481 4.66691 12.9888C3.68021 12.3295 2.91118 11.3925 2.45705 10.2961C2.00293 9.19974 1.88411 7.99334 2.11562 6.82946C2.34713 5.66557 2.91857 4.59647 3.75769 3.75736C4.5968 2.91824 5.6659 2.3468 6.82979 2.11529C7.99367 1.88377 9.20007 2.00259 10.2964 2.45672C11.3928 2.91085 12.3299 3.67988 12.9891 4.66658C13.6484 5.65327 14.0003 6.81331 14.0003 8C13.9986 9.59075 13.3659 11.1159 12.241 12.2407C11.1162 13.3655 9.59109 13.9982 8.00033 14ZM12.0003 8C12.0003 8.17681 11.9301 8.34638 11.8051 8.4714C11.68 8.59643 11.5105 8.66666 11.3337 8.66666H8.00033C7.82352 8.66666 7.65395 8.59643 7.52892 8.4714C7.4039 8.34638 7.33366 8.17681 7.33366 8V4C7.33366 3.82319 7.4039 3.65362 7.52892 3.52859C7.65395 3.40357 7.82352 3.33333 8.00033 3.33333C8.17714 3.33333 8.34671 3.40357 8.47173 3.52859C8.59676 3.65362 8.667 3.82319 8.667 4V7.33333H11.3337C11.5105 7.33333 11.68 7.40357 11.8051 7.52859C11.9301 7.65362 12.0003 7.82319 12.0003 8Z" fill="#848FA3"/>
                                                        </svg>
                                                        &nbsp;<?= Yii::$app->devSet->getTranslate('time') ?>
                                                    </span>
                                                <p><?= $classAlignedStartDateTime->format('H:i') ?> - <?= $classAlignedEndDateTime->format('H:i') ?></p>
                                            </div>
                                        </section>

                                        <section class="attendees">
                                            <div class="display-flex">
                                                <?php $counter = 0; ?>
                                                <?php foreach($conversationUsers as $conversationUser) { ?>
                                                    <?php if($conversationUser->action == 'reserve') { ?>
                                                        <?php
                                                            $counter++;
                                                            $colors = [1 => '#FBBC04', 2 => '#35C9D4', 3 => '#DD33AC', 4 => '#00B67A', 5 => '#EE6011', 6 => '#1877F2', 7 => '#763BE0', 8 => '#2B82ED'];
                                                            $randColor = shuffle($colors);
                                                            $color = $colors[$randColor];
                                                        ?>
                                                        <?php if($counter <= GROUP_SIZE) { ?>
                                                            <div class="attendee" data-user-id="<?= $conversationUser->user->id ?>" style="background-color: <?php if($conversationUser->user->userProfile->color == null) { ?> <?= $color ?> <?php } else { ?> <?= $conversationUser->user->userProfile->color ?> <?php } ?>;">
                                                                <?= strtoupper(substr($conversationUser->user->username, 0,1)) ?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php for($counter += 1; $counter <= GROUP_SIZE; $counter++) { ?>
                                                    <div class="waiting-attendee">
                                                        <img src="<?=Yii::getAlias('@web');?>/img/dashboard/add-profile.svg" alt="">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </section>

                                        <section class="action-section">
                                            <section class="reserve-class <?php if($aboutClass == 'reserved') { ?> display-none <?php } ?>">
                                                <div class="btn reserve-room-button">
                                                    <?= Yii::$app->devSet->getTranslate('reserve') ?>
                                                    <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                                </div>
                                            </section>

                                            <section class="enter-room-section <?php if($aboutClass != 'reserved') { ?> display-none <?php } ?>">
                                                <div class="display-flex">
                                                    <div class="btn enter-room-button">
                                                        <?= Yii::$app->devSet->getTranslate('enterRoom') ?>
                                                        <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                                    </div>
                                                    <div class="btn cancel-room-button position-relative">
                                                        <svg class="position-absolute" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5733 5.01755C14.7866 4.80428 14.7863 4.44504 14.5646 4.22331L13.7765 3.43527C13.5453 3.20406 13.1992 3.20964 12.9823 3.42656L8.99984 7.40902L5.01738 3.42656C4.80411 3.21329 4.44487 3.21354 4.22314 3.43527L3.4351 4.22331C3.20389 4.45452 3.20947 4.80063 3.42639 5.01755L7.40885 9.00001L3.42639 12.9825C3.21312 13.1957 3.21337 13.555 3.4351 13.7767L4.22314 14.5647C4.45435 14.796 4.80046 14.7904 5.01738 14.5735L8.99984 10.591L12.9823 14.5735C13.1956 14.7867 13.5548 14.7865 13.7765 14.5647L14.5646 13.7767C14.7958 13.5455 14.7902 13.1994 14.5733 12.9825L10.5908 9.00001L14.5733 5.01755Z" fill="#848FA3"/>
                                                        </svg>
                                                        <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true" style="margin: 0;"></span>
                                                    </div>
                                                </div>
                                                <div class="tik-tok w-100">
                                                    <div class="display-flex" style="justify-content: center;height: 100%">
                                                        <div class="counter">
                                                            <b class="hours">00</b>
                                                            <p><?= Yii::$app->devSet->getTranslate('hours') ?></p>
                                                        </div>
                                                        <div class="counter">
                                                            <b class="minutes">00</b>
                                                            <p><?= Yii::$app->devSet->getTranslate('minutes') ?></p>
                                                        </div>
                                                        <div class="counter">
                                                            <b class="seconds">00</b>
                                                            <p><?= Yii::$app->devSet->getTranslate('seconds') ?></p>
                                                        </div>
                                                    </div>
                                            </section>
                                        </section>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>

                <?php } ?>
                <?php } ?>
            </div>
        </div>

        <!-------------------- No Reserved Class -------------------->
        <div class="no-reserved-class row <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty' AND ($reservedClassExists == true)) { ?> display-none <?php } ?>">
            <div class="col-12" align="center">
                <div class="no-classes">
                    <img width="107" src="/img/dashboard/emptyClass.svg" alt="">
                    <h5>
                        <?= Yii::$app->devSet->getTranslate('noReservedClasses') ?>
                    </h5>
                    <p>
                        <?= Yii::$app->devSet->getTranslate('goToAvailableClasses') ?>
                    </p>

                    <button class="btn g-to-classes" id="go-to-classes">
                        <?= Yii::$app->devSet->getTranslate('goToAvailableClassesButton') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>




<!------------------  Modal's  -------------------->
<?php if((Yii::$app->user->identity->userParameters->proficiency != 'no' AND Yii::$app->user->identity->userParameters->proficiency != 'level') AND (Yii::$app->user->identity->userParameters->currentLevel != 'empty')) { ?>
    <script>
        $(document).ready(function () {
            setTimeout(function () { $('#boarding-trigger').click() }, 1000)
        })
    </script>
    <button type="button" id="boarding-trigger" class="display-none" data-bs-toggle="modal" data-bs-target="#boarding">ok</button>
    <div class="modal fade response-modal show" id="boarding" style="background-color: rgba(0, 0, 0, 0.52);" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div id="boarding-carousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">

                        <div class="carousel-item <?php if(Yii::$app->user->identity->userParameters->proficiency == 'level-start-date') { ?> active <?php } ?>">
                            <div class="row w-100">
                                <div class="col-12" align="center">
                                    <br>
                                    <img src="/img/dashboard/level-growth.svg" alt="">
                                    <div style="margin-top:10px;"></div>
                                    <p style="color: #202734;font-weight: 600;font-size: 22px;">
                                        <?= Yii::$app->devSet->getTranslate('resultOfLevelTest') ?>
                                    </p>
                                    <button type="button" class="btn" style="border-radius: 8px;height: 48px;border: 1px solid #CBD1D3;background: #FFFFFF;">
                                        <span style="font-weight: 500;color: #00B67A;font-size: 20px;text-transform: uppercase;">
                                            &nbsp;<?= Yii::$app->user->identity->userParameters->currentLevel ?>&nbsp;
                                        </span>
                                    </button>
                                    <div style="margin-top:40px;"></div>
                                </div>
                            </div>

                            <div class="modal-footer" style="padding: 0;">
                                <div class="row w-100">
                                    <div class="col-12">
                                        <label style="color: #646E82;font-size: 14px;">
                                            <?= Yii::$app->devSet->getTranslate('standardCEFR') ?>
                                        </label>
                                        <div style="margin-top:5px;"></div>
                                        <button type="button" class="btn primary-button next-to-boarding w-100">
                                            <?= Yii::$app->devSet->getTranslate('next') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php
                            $nextNClasses = 3;
                            $schedulesArray = array_map('intval', str_split(Yii::$app->user->identity->userParameters->currentSchedule));

                            $today = new DateTime('now');
                            $startDate = (Yii::$app->user->identity->userParameters->startDate != null) ? new DateTime(Yii::$app->user->identity->userParameters->startDate) : $today;
                            $startDate = ($startDate < $today) ? $today : $startDate;

                            $nextAvailableDates = array();

                            while (sizeof($nextAvailableDates) < $nextNClasses) {
                                if(in_array($startDate->format('w'), $schedulesArray)) {
                                    array_push($nextAvailableDates, $startDate->format('Y-m-d'));
                                }

                                $startDate->add(new DateInterval('P1D'));
                            }
                        ?>
                        <div align="center" class="carousel-item <?php if(Yii::$app->user->identity->userParameters->proficiency == 'start-date') { ?> active <?php } ?>">
                            <br>
                            <img src="/img/dashboard/start-date.svg" alt="">
                            <div style="margin-top:10px;"></div>
                            <p style="color: #202734;font-weight: 600;font-size: 22px;">
                                <?= Yii::$app->devSet->getTranslate('whenToStart') ?>
                            </p>
                            <div id="start-date-select" class="select-date dropdown">
                                <button id="dropdownMenuButton3333" class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: 0 4px 8px rgba(16, 24, 64, 0.08);">
                                    <img src="/img/dashboard/calendar-date.svg" alt="">
                                    &nbsp;<span class="selected-start-date">
                                        <?php $objectStartDate = new DateTime($nextAvailableDates[0].' '.$localDateTime->format('H:i')); ?>
                                        <?php $alignedStartDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($objectStartDate, $userTimeZone); ?>
                                        <?= $alignedStartDate->format('d.m.Y')  ?> - <?= $alignedStartDate->format('l') ?>
                                    </span>&nbsp;
                                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.3335 1L5.3335 5L1.3335 1" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton3333" style="">
                                    <?php foreach ($nextAvailableDates as $date) { ?>
                                        <?php $currentObjectStartDate = new DateTime($date.' '.$localDateTime->format('H:i')); ?>
                                        <?php $currentObjectStartDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($currentObjectStartDate, $userTimeZone) ?>
                                        <li>
                                            <a class="dropdown-item <?php if($nextAvailableDates[0] == $date) { ?>active<?php } ?>"
                                               data-start-class-date="<?= $currentObjectStartDate->format('Y-m-d') ?>">
                                                <svg class="display-none" width="11" height="9" viewBox="0 0 11 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.28033 4.77513C0.987437 4.48223 0.512563 4.48223 0.21967 4.77513C-0.0732233 5.06802 -0.0732233 5.54289 0.21967 5.83579L2.94202 8.55814C3.23491 8.85103 3.70979 8.85103 4.00268 8.55814L10.447 2.11383C10.7399 1.82093 10.7399 1.34606 10.447 1.05317C10.1541 0.760273 9.67923 0.760273 9.38634 1.05317L3.47235 6.96715L1.28033 4.77513Z" fill="#1676F3"></path>
                                                </svg>
                                                <text><?= $currentObjectStartDate->format('d.m.Y')  ?> - <?= $currentObjectStartDate->format('l') ?></text>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div style="margin-top:40px;"></div>

                            <div class="modal-footer" style="padding: 0;">
                                <label class="cursor-pointer" style="color: #646E82;font-size: 14px;">
                                    <input class="form-check-input-available" type="checkbox" value="" id="flexCheckDefault">
                                    <?= Yii::$app->devSet->getTranslate('availableToStart') ?>
                                </label>
                                <div style="margin-top:5px;"></div>
                                <button type="button" class="btn primary-button confirm-boarding-time-range next-to-time-range w-100 disabled-visually" disabled>
                                    <?= Yii::$app->devSet->getTranslate('next') ?>&nbsp;&nbsp;
                                    <div class="spinner-grow text-light display-none" role="status" style="height: 15px;width: 15px;"></div>
                                </button>
                            </div>
                        </div>



                        <div class="carousel-item">
                            <div class="row">
                                <div class="col-12" align="center">
                                    <br>
                                    <div style="padding: 0 16px;">
                                        <img src="/img/dashboard/boarding-completed.svg" alt="">
                                        <div style="margin-top:10px;"></div>
                                        <p style="color: #202734;font-weight: 600;font-size: 22px;">
                                            <?= Yii::$app->devSet->getTranslate('everythingIsReady') ?>!
                                        </p>
                                        <table class="w-100 table" style="border-collapse: separate; border: 1px solid #E0E2E7;border-radius: 8px;background: #F3F3F3;">
                                            <tr>
                                                <td style="border-right: 1px solid #E0E2E7;">
                                                    <img src="/img/dashboard/confirmed-calendar.svg" alt="">
                                                    &nbsp;
                                                    <span style="color: #000000;font-size: 14px;">
                                                        <?= Yii::$app->devSet->getTranslate('startDate') ?>:
                                                    </span>
                                                </td>
                                                <td>
                                                    <span id="chosenDate" style="color: #000000;font-size: 14px;font-weight: bold;">...</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-right: 1px solid #E0E2E7;border-bottom: 0;">
                                                    <img width="18" src="/img/dashboard/clock-gray.svg" alt="">
                                                    &nbsp;
                                                    <span style="color: #000000;font-size: 14px;">
                                                        <?= Yii::$app->devSet->getTranslate('scheduleForLessons') ?>:
                                                    </span>
                                                </td>
                                                <td style="border-bottom: 0;">
                                                    <span id="chosenTimeRange" style="color: #000000;font-size: 14px;font-weight: bold;">...</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div style="margin-top:20px;"></div>
                                </div>
                            </div>

                            <div class="modal-footer" style="padding: 0;">
                                <div class="row w-100">
                                    <div class="col-12" align="center">
                                        <label style="color: #646E82;font-size: 14px;">
                                            ✔ &nbsp;<?= Yii::$app->devSet->getTranslate('boardingInformationWasSentViaEmail') ?>.
                                        </label>
                                        <div style="margin-top:5px;"></div>
                                        <button type="button" class="btn primary-button boarding-finish-button w-100" data-bs-dismiss="modal" style="background-color: #00B67A;">
                                            <?= Yii::$app->devSet->getTranslate('finish') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>


<?php if( ((Yii::$app->user->identity->userParameters->proficiency == 'level') AND (Yii::$app->user->identity->userParameters->currentLevel != 'empty')) ) { ?>
    <?php
        $userParameters = UserParameters::findOne(['userId' => Yii::$app->user->id]);
        $userParameters->proficiency = 'no';
        $userParameters->save(false);
    ?>
    <script>
        $(document).ready(function () {
            setTimeout(function () { $('#pre-boarding-trigger').click() }, 1000)
        })
    </script>
    <button type="button" id="pre-boarding-trigger" class="display-none" data-bs-toggle="modal" data-bs-target="#boarding">ok</button>
    <div class="modal fade response-modal show level-time-range-select" id="boarding" style="background-color: rgba(0, 0, 0, 0.52);" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="pre-boarding-carousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner">

                            <div class="carousel-item active" align="center">
                                <br>
                                <img src="/img/dashboard/level-growth.svg" alt="">
                                <div style="margin-top:10px;"></div>
                                <p style="color: #202734;font-weight: 600;font-size: 22px;">
                                    <?= Yii::$app->devSet->getTranslate('resultOfLevelTest') ?>
                                </p>
                                <button type="button" class="btn" style="border-radius: 8px;height: 48px;border: 1px solid #CBD1D3;background: #FFFFFF;">
                                <span style="font-weight: 500;color: #00B67A;font-size: 20px;text-transform: uppercase;">
                                    &nbsp;<?= Yii::$app->user->identity->userParameters->currentLevel ?>&nbsp;
                                </span>
                                </button>
                                <div style="margin-top:40px;"></div>

                                <div class="modal-footer" style="padding: 0;">
                                    <label style="color: #646E82;font-size: 14px;">
                                        <?= Yii::$app->devSet->getTranslate('standardCEFR') ?>
                                    </label>
                                    <div style="margin-top:5px;"></div>
                                    <button type="button" class="btn primary-button w-100" data-bs-dismiss="modal">
                                        <?= Yii::$app->devSet->getTranslate('close') ?>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>



<?php
    $showFeedback = false;

    $conversationUsers = ConversationUsers::find()->where([
        'userId' => Yii::$app->user->id,
        'action' => 'enter'
    ])->orderBy(['conversationDate' => SORT_DESC])->limit(1)->one();

    if ($conversationUsers != null) {
        if (!Feedback::find()->where(['classId' => $conversationUsers->conversation->id])->exists()) {
            $showFeedback = true;
            $feedbackTopic = Yii::$app->devSet->todayTopic(Yii::$app->user->identity->userParameters->currentLevel, $conversationUsers->conversationDate);
        }
    }
?>

<?php if ($showFeedback) { ?>
<!------------------  Feedback Modal  -------------------->
<style>
    #feedback-modal .tutor-image {
        width: 77px !important;
        height: 77px !important;
    }
    #feedback-modal .story {
        width: 83px !important;
        height: 83px !important;
    }
    .f-description {
        align-self: center;
    }
    .f-presentation {
        margin-right: 5px;
    }

    .rate {
        float: left;
        height: 46px;
    }
    .rate:not(:checked) > input {
        position:absolute;
        top:-9999px;
    }
    .rate:not(:checked) > label {
        float:right;
        width:1em;
        margin-right: 3px;
        overflow:hidden;
        white-space:nowrap;
        cursor:pointer;
        font-size:30px;
        color:#ccc;
    }
    .rate:not(:checked) > label:before {
        content: '★ ';
    }
    .rate > input:checked ~ label {
        color: #ffc700;
    }
    .rate:not(:checked) > label:hover,
    .rate:not(:checked) > label:hover ~ label {
        color: #deb217;
    }
    .rate > input:checked + label:hover,
    .rate > input:checked + label:hover ~ label,
    .rate > input:checked ~ label:hover,
    .rate > input:checked ~ label:hover ~ label,
    .rate > label:hover ~ input:checked ~ label {
        color: #c59b08;
    }

    .f-textarea {
        width: 100%;
        min-height: 130px;
        background-color: #F7F9FD;
        border: 1px solid #e5e8f1;
        border-radius: 8px;
        margin-top: 5px;
        padding: 6px 10px;
    }
    .f-textarea::placeholder {
        opacity: 0.5;
    }
    .feedback-info {
        border-radius: 8px;
        background-color: #F7F9FD;
        border: 1px solid #e5e8f1;
        padding: 5px 10px;
    }
</style>
<script>
    $(document).ready(function () {
        $('#approve-feedback').click(function () {
            let starsCount = $('.rate input[name="rate"]:checked').val()
            let comment = $('.f-textarea').val().trim()

            if (starsCount === undefined) {
                return false
            }

            $.ajax({
                url : _feedbackConfirm,
                type : 'POST',
                data : {
                    '_csrf-frontend': _csrf_frontend,
                    'classId': '<?= $conversationUsers->conversation->id ?>',
                    'topic': '<?= $feedbackTopic['description'] ?>',
                    'starsCount': starsCount,
                    'comment': comment,
                },
                async: true,
                beforeSend: function() {
                    $('#feedback-modal .spinner-grow').removeClass('display-none')
                    $('.alert-danger').addClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)

                    if (data.success === false) {
                        $('#feedback-modal .alert-danger').html(data.error)
                        $('#feedback-modal .alert-danger').slideDown(250)
                    } else {
                        $('#feedback-modal .alert-danger').slideUp(250)
                        $('#feedback-modal .alert-danger').addClass('display-none')

                        $('#feedback-modal .close').click()
                    }
                },
                error : function(request, error) {
                    console.log(error);
                    console.log(request);
                    alert(request.statusText);
                },
                complete: function() {
                    $('#feedback-modal .spinner-grow').addClass('display-none')
                }
            });
        })

        setTimeout(function () {
            $('#feedback').click()
        }, 2000)
    })
</script>
<button type="button" id="feedback" class="display-none" data-bs-toggle="modal" data-bs-target="#feedback-modal">feedback</button>
<div class="modal fade response-modal" id="feedback-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 410px;">
        <div class="modal-content">

            <div class="modal-body">
                <div class="f-header display-flex">
                    <div class="f-presentation">
                        <div class="story clickable-story"
                             data-bs-toggle="modal"
                             data-bs-target="#exampleModal5"
                             data-name="<?= $conversationUsers->conversation->teacher->teacherName ?>"
                             data-country="<?= $conversationUsers->conversation->teacher->country ?>"
                             data-experience="<?= $conversationUsers->conversation->teacher->experience ?>"
                             data-description="<?= $conversationUsers->conversation->teacher['description_'.Yii::$app->language] ?>"
                             data-presentation="<?= $conversationUsers->conversation->teacher->presentation ?>">
                            <img class="tutor-image" src="http://localhost/backend/web/<?= $conversationUsers->conversation->teacher->image ?>" alt="">
                        </div>
                    </div>

                    <div class="f-description">
                        <div class="t-info">
                            <h5 style="font-weight: 700;font-size: 16px;">
                                <?= Yii::$app->devSet->getTranslate('tutor') ?>: <?= $conversationUsers->conversation->teacher->teacherName ?>
                            </h5>
                            <span style="font-weight: 400;font-size: 16px;">
                                <?= $feedbackTopic['description'] ?>
                            </span>
                            <br>
                            <span style="color: #3CA9F8;font-weight: 600;font-size: 14px;">
                                <?php
                                    $classAlignedDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($conversationUsers->conversation->date . ' ' . $localDateTime->format('H:i')), $userTimeZone);
                                    $classAlignedStartDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($conversationUsers->conversation->date . ' ' . $conversationUsers->conversation->startsAt), $userTimeZone);
                                    $classAlignedEndDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($conversationUsers->conversation->date . ' ' . $conversationUsers->conversation->endsAt), $userTimeZone);
                                ?>
                                <?= $classAlignedDate->format('M d, Y') ?> | <?= $classAlignedStartDateTime->format('H:i') ?>-<?= $classAlignedEndDateTime->format('H:i') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px"></div>

                <div class="stars">
                    <span style="font-weight: 400;font-size: 16px;">
                        <?= Yii::$app->devSet->getTranslate('howWasYourLesson') ?>?
                    </span>
                    <br>
                    <div class="rate">
                        <input type="radio" id="star5" name="rate" value="5" />
                        <label for="star5" title="text">5 stars</label>
                        <input type="radio" id="star4" name="rate" value="4" />
                        <label for="star4" title="text">4 stars</label>
                        <input type="radio" id="star3" name="rate" value="3" />
                        <label for="star3" title="text">3 stars</label>
                        <input type="radio" id="star2" name="rate" value="2" />
                        <label for="star2" title="text">2 stars</label>
                        <input type="radio" id="star1" name="rate" value="1" />
                        <label for="star1" title="text">1 star</label>
                    </div>
                </div>

                <div style="clear: both;"></div>
                <div style="margin-top: 15px"></div>

                <div class="f-comment">
                    <span style="font-weight: 400;font-size: 16px;">
                        <?= Yii::$app->devSet->getTranslate('lessonOpinion') ?>:
                    </span>
                    <br>

                    <textarea class="f-textarea" maxlength="5000" placeholder="<?= Yii::$app->devSet->getTranslate('postYourThoughts') ?>"></textarea>
                </div>

                <div class="alert alert-danger display-none" role="alert" style="margin-bottom: 0;">
                    ...
                </div>
            </div>

            <div class="modal-footer">
                <div class="display-flex w-100" style="flex-direction: row;justify-content: space-between;">
                    <div style="margin-right: 24px;width: 100%;">
                        <button type="button" class="btn close w-100" data-bs-dismiss="modal">
                            <?= Yii::$app->devSet->getTranslate('close') ?>
                        </button>
                    </div>
                    <div style="width: 150%;">
                        <button type="button" id="approve-feedback" class="btn primary-button w-100">
                            <?= Yii::$app->devSet->getTranslate('confirm') ?>&nbsp;
                            <div class="spinner-grow text-light display-none" role="status" style="width: 16px;height: 16px;"></div>
                        </button>
                    </div>
                </div>

                <div style="margin-top: 10px"></div>

                <div class="feedback-info display-flex">
                    <div style="margin-right: 10px;margin-top: 2px;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 0C4.486 0 0 4.486 0 10C0 15.514 4.486 20 10 20C15.514 20 20 15.514 20 10C20 4.486 15.514 0 10 0ZM10 4C11.7 4 13 5.3 13 7V8C13.6 8 14 8.4 14 9V13C14 13.6 13.6 14 13 14H7C6.4 14 6 13.6 6 13V9C6 8.4 6.4 8 7 8V7C7 5.3 8.3 4 10 4ZM10 5.5C9.2 5.5 8.5 6.2 8.5 7V8H10H11.5V7C11.5 6.2 10.8 5.5 10 5.5ZM10 10C9.73478 10 9.48043 10.1054 9.29289 10.2929C9.10536 10.4804 9 10.7348 9 11C9 11.2652 9.10536 11.5196 9.29289 11.7071C9.48043 11.8946 9.73478 12 10 12C10.2652 12 10.5196 11.8946 10.7071 11.7071C10.8946 11.5196 11 11.2652 11 11C11 10.7348 10.8946 10.4804 10.7071 10.2929C10.5196 10.1054 10.2652 10 10 10Z" fill="#00B67A"/>
                        </svg>
                    </div>
                    <div>
                        <span style="font-weight: 400;font-size: 13px;">
                            <?= Yii::$app->devSet->getTranslate('anonymousComplaints') ?>
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php } ?>



<!------------------  Slot Modal  -------------------->
<link href="/css/payment/payment.css" rel="stylesheet">
<script>
    $(document).ready(function () {
        let packetId = null
        let max = parseInt('<?= strlen(Yii::$app->user->identity->userParameters->currentSchedule) ?>')
        let selected = 0
        let weekdays = null
        let timeRange = null


        $('[data-popup-id="time-availability"]').click(function () {
            /*  Refresh fields  */
            $('selected').html(0)
            $('input[name="weekday"]').prop('disabled', false).prop('checked', false)
            $('input[name="weekday"] + label').removeClass('disabled-visually')
            $('.time-ranges .form-check-input').prop('checked', false)

            $('max').html(max)
            $('#slot').click()
        })


        $('input[name="weekday"]').click(function () {
            selected = $('input[name="weekday"]:checked').length

            $('selected').html(selected)

            if (selected === max) {
                $('input[name="weekday"]:not(:checked)').prop('disabled', true)
                $('input[name="weekday"]:not(:checked) + label').addClass('disabled-visually')
            } else if (selected < max) {
                $('input[name="weekday"]:not(:checked)').prop('disabled', false)
                $('input[name="weekday"]:not(:checked) + label').removeClass('disabled-visually')
            }
        })


        $('#approve-schedule').click(function () {
            if (selected !== max) {
                alert('Please select all days of the week')
                return false
            }

            if (!$('input[name="time-range"]:checked').length) {
                alert('Please select a time range')
                return false
            }

            weekdays = $('input[name="weekday"]:checked').map(function () {
                return $(this).val()
            }).get()

            timeRange = $('input[name="time-range"]:checked').val()

            console.log(weekdays + ' - ' + timeRange + ' - ' + packetId + ' - ' + _timeAvailability)

            $.ajax({
                url : _timeAvailability,
                type : 'POST',
                data : {
                    '_csrf-frontend': _csrf_frontend,
                    'weekdays': weekdays,
                    'timeRange': timeRange,
                },
                async: true,
                beforeSend: function() {
                    $('#approve-schedule .spinner-grow').removeClass('display-none')
                    $('.alert-danger').addClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)

                    if(!data.success) {
                        $('.alert-danger').html(data.error).show(100)
                    } else {
                        $('.alert-danger').hide(0)
                        $('#slot-modal .close').click()

                        location.reload()
                        window.location.reload()
                    }
                },
                error : function(request, error) {
                    console.log(error);
                    console.log(request);
                    alert(request.statusText);
                },
                complete: function() {
                    $('#approve-schedule .spinner-grow').addClass('display-none')
                }
            });
        })
    })
</script>
<button type="button" id="slot" class="display-none" data-bs-toggle="modal" data-bs-target="#slot-modal">ok</button>
<div class="modal fade response-modal" id="slot-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="padding: 20px 25px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.77778 5.9979L11.6352 2.14181C12.1216 1.65561 12.1216 0.850856 11.6352 0.364652C11.1488 -0.121551 10.3438 -0.121551 9.85744 0.364652L6 4.22075L2.14256 0.364652C1.65618 -0.121551 0.851153 -0.121551 0.36478 0.364652C-0.121593 0.850856 -0.121593 1.65561 0.36478 2.14181L4.22222 5.9979L0.36478 9.854C-0.121593 10.3402 -0.121593 11.145 0.36478 11.6312C0.616352 11.8826 0.93501 12 1.25367 12C1.57233 12 1.89098 11.8826 2.14256 11.6312L6 7.77506L9.85744 11.6312C10.109 11.8826 10.4277 12 10.7463 12C11.065 12 11.3836 11.8826 11.6352 11.6312C12.1216 11.145 12.1216 10.3402 11.6352 9.854L7.77778 5.9979Z" fill="#646E82"/>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <h4 style="text-align: left !important;"><?= Yii::$app->devSet->getTranslate('setYourSchedule') ?></h4>

                <div style="margin-top: 42px"></div>

                <div class="weekdays-block">
                    <div class="weekdays-description display-flex justify-content-between">
                        <button class="btn no-style">
                            <h5 class="cursor-pointer">
                                <?= Yii::$app->devSet->getTranslate('selectTheDaysOfTheWeek') ?>:
                            </h5>
                        </button>
                        <div class="counter">
                            <selected>0</selected>/<max><?= strlen(Yii::$app->user->identity->userParameters->currentSchedule) ?></max>
                        </div>
                    </div>
                    <div class="weekdays display-flex justify-content-between">
                        <?php foreach ($weekdays[$lang] as $key => $value)  { ?>
                            <div class="form-check">
                                <input class="display-none" type="checkbox" value="<?= ($key + 1) ?>" name="weekday" id="weekday<?= ($key + 1) ?>">
                                <label class="" for="weekday<?= ($key + 1) ?>">
                                    <?= $value ?>
                                </label>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="time-ranges-description display-flex">
                        <button class="btn no-style">
                            <h5 class="cursor-pointer">
                                <?= Yii::$app->devSet->getTranslate('selectTimeRange') ?>:
                            </h5>
                        </button>
                    </div>
                    <div class="time-ranges">
                        <?php foreach ($availableTimes as $key => $value) {
                            $homeLandTimeStart = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$value['start']);
                            $homeLandTimeStartV = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$value['startV']);
                            $homeLandTimeEnd = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$value['end']);
                            $homeLandTimeEndV = new DateTime(Yii::$app->devSet->getDateByTimeZone(Yii::$app->getTimeZone())->format('Y-m-d').$value['endV']);

                            $userLandTimeStart = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($homeLandTimeStart, $userTimeZone);
                            $userLandTimeStartV = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($homeLandTimeStartV, $userTimeZone);
                            $userLandTimeEnd = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($homeLandTimeEnd, $userTimeZone);
                            $userLandTimeEndV = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($homeLandTimeEndV, $userTimeZone);

                            $dtz = new DateTimeZone($userTimeZone);
                            $time_in_sofia = new DateTime('now', $dtz);
                            $GMT = $dtz->getOffset( $time_in_sofia )/60/60;
                            //debug($GMT);
                            ?>
                            <?php if(($homeLandTimeStart->format('d') == $userLandTimeStart->format('d')) AND ($homeLandTimeEnd->format('d') == $userLandTimeEnd->format('d'))) { ?>
                                <div class="form-check">
                                    <input class="form-check-input display-none"
                                           type="radio"
                                           name="time-range"
                                           id="_exampleRadios<?= $key.$key ?>"
                                           value="<?= $value['start'] ?>-<?= $value['end'] ?>"
                                    >
                                    <label class="form-check-label" for="_exampleRadios<?= $key.$key ?>">
                                        <?php if($GMT >= 2 AND $GMT <= 6) { ?>
                                            <?php  if($key == 1) { ?>
                                                <img src="/img/payment/9-12.svg" alt="sun icon">
                                            <?php } ?>
                                            <?php  if($key == 2) { ?>
                                                <img src="/img/payment/15-18.svg" alt="sun icon">
                                            <?php } ?>
                                            <?php  if($key == 3) { ?>
                                                <img src="/img/payment/18-21.svg" alt="sun icon">
                                            <?php } ?>
                                            <?php  if($key == 4) { ?>
                                                <img src="/img/payment/21-24.svg" alt="sun icon">
                                            <?php } ?>
                                        <?php } ?>
                                        <span><?= $userLandTimeStartV->format('H:i') ?> - <?= $userLandTimeEndV->format('H:i') ?></span>
                                    </label>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="alert alert-danger display-none" role="alert" style="margin-bottom: 0;">
                    ...
                </div>
            </div>

            <div class="modal-footer">
                <div class="display-flex w-100" style="flex-direction: row;justify-content: space-between;">
                    <div style="margin-right: 24px;width: 100%;">
                        <button type="button" class="btn close w-100" data-bs-dismiss="modal">
                            <?= Yii::$app->devSet->getTranslate('close') ?>
                        </button>
                    </div>
                    <div style="width: 150%;">
                        <button type="button" id="approve-schedule" class="btn primary-button w-100">
                            <?= Yii::$app->devSet->getTranslate('confirm') ?>&nbsp;
                            <div class="spinner-grow text-light display-none" role="status" style="width: 16px;height: 16px;"></div>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<button type="button" class="display-none" data-bs-toggle="modal" data-bs-target="#time-range-change-confirm"></button>
<div class="modal fade response-modal" id="time-range-change-confirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding: 20px 25px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.77778 5.9979L11.6352 2.14181C12.1216 1.65561 12.1216 0.850856 11.6352 0.364652C11.1488 -0.121551 10.3438 -0.121551 9.85744 0.364652L6 4.22075L2.14256 0.364652C1.65618 -0.121551 0.851153 -0.121551 0.36478 0.364652C-0.121593 0.850856 -0.121593 1.65561 0.36478 2.14181L4.22222 5.9979L0.36478 9.854C-0.121593 10.3402 -0.121593 11.145 0.36478 11.6312C0.616352 11.8826 0.93501 12 1.25367 12C1.57233 12 1.89098 11.8826 2.14256 11.6312L6 7.77506L9.85744 11.6312C10.109 11.8826 10.4277 12 10.7463 12C11.065 12 11.3836 11.8826 11.6352 11.6312C12.1216 11.145 12.1216 10.3402 11.6352 9.854L7.77778 5.9979Z" fill="#646E82"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body" style="padding: 0 10px 10px;">
                <h4>
                    <img width="25" src="/img/dashboard/are-you-sure.svg" alt="">
                    &nbsp;<?= Yii::$app->devSet->getTranslate('areYouSure') ?>
                </h4>
                <div style="margin-top: 15px;"></div>
                <p><?= Yii::$app->devSet->getTranslate('ifYouChangeSegment') ?></p>
            </div>
            <div class="modal-footer" style="padding: 10px 14px;">
                <div class="row w-100">
                    <div class="col-5">
                        <button type="button" class="btn close w-100" data-bs-dismiss="modal">
                            <?= Yii::$app->devSet->getTranslate('cancel') ?>
                        </button>
                    </div>
                    <div class="col-7">
                        <button type="button" class="btn primary-button w-100">
                            <?= Yii::$app->devSet->getTranslate('confirm') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<button type="button" class="display-none" data-bs-toggle="modal" data-bs-target="#class-response"></button>
<div class="modal fade response-modal" id="class-response" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding: 20px 25px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.77778 5.9979L11.6352 2.14181C12.1216 1.65561 12.1216 0.850856 11.6352 0.364652C11.1488 -0.121551 10.3438 -0.121551 9.85744 0.364652L6 4.22075L2.14256 0.364652C1.65618 -0.121551 0.851153 -0.121551 0.36478 0.364652C-0.121593 0.850856 -0.121593 1.65561 0.36478 2.14181L4.22222 5.9979L0.36478 9.854C-0.121593 10.3402 -0.121593 11.145 0.36478 11.6312C0.616352 11.8826 0.93501 12 1.25367 12C1.57233 12 1.89098 11.8826 2.14256 11.6312L6 7.77506L9.85744 11.6312C10.109 11.8826 10.4277 12 10.7463 12C11.065 12 11.3836 11.8826 11.6352 11.6312C12.1216 11.145 12.1216 10.3402 11.6352 9.854L7.77778 5.9979Z" fill="#646E82"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body" style="padding: 15px 10px 0;">
                <h4><?= Yii::$app->devSet->getTranslate('haveNoBalance') ?></h4>
                <p><?= Yii::$app->devSet->getTranslate('enterThePaymentSection') ?></p>
            </div>
            <div class="modal-footer" style="padding: 20px 14px;">
                <div class="row w-100">
                    <div class="col-5">
                        <button type="button" class="btn close w-100" data-bs-dismiss="modal">
                            <?= Yii::$app->devSet->getTranslate('close') ?>
                        </button>
                    </div>
                    <div class="col-7">
                        <a href="<?= Url::to(['payment/index'], true) ?>">
                            <button type="button" class="btn primary-button w-100">
                                <?= Yii::$app->devSet->getTranslate('goToPayment') ?>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<button type="button" class="display-none" data-bs-toggle="modal" data-bs-target="#cancel-confirm"></button>
<div class="modal fade response-modal" id="cancel-confirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding: 20px 25px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.77778 5.9979L11.6352 2.14181C12.1216 1.65561 12.1216 0.850856 11.6352 0.364652C11.1488 -0.121551 10.3438 -0.121551 9.85744 0.364652L6 4.22075L2.14256 0.364652C1.65618 -0.121551 0.851153 -0.121551 0.36478 0.364652C-0.121593 0.850856 -0.121593 1.65561 0.36478 2.14181L4.22222 5.9979L0.36478 9.854C-0.121593 10.3402 -0.121593 11.145 0.36478 11.6312C0.616352 11.8826 0.93501 12 1.25367 12C1.57233 12 1.89098 11.8826 2.14256 11.6312L6 7.77506L9.85744 11.6312C10.109 11.8826 10.4277 12 10.7463 12C11.065 12 11.3836 11.8826 11.6352 11.6312C12.1216 11.145 12.1216 10.3402 11.6352 9.854L7.77778 5.9979Z" fill="#646E82"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body" style="padding: 15px 10px 0;">
                <h4><?= Yii::$app->devSet->getTranslate('doYouWantCancelTheReserve') ?></h4>
                <p><?= Yii::$app->devSet->getTranslate('afterCancelLesson') ?></p>
            </div>
            <div class="modal-footer" style="padding: 20px 14px;">
                <div class="row w-100">
                    <div class="col-5">
                        <button type="button" class="btn close w-100" data-bs-dismiss="modal">
                            <?= Yii::$app->devSet->getTranslate('cancelNo') ?>
                        </button>
                    </div>
                    <div class="col-7">
                        <button type="button" class="btn primary-button w-100">
                            <?= Yii::$app->devSet->getTranslate('yes') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!------------------  Presentation Modal  -------------------->
<div class="modal fade" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="padding: 0!important;">
    <div class="modal-dialog modal-fullscreen-sm-down my-modal-fullscreen" style="max-width: 550px;">
        <div class="modal-content large my-modal-content" style="border: none; height: 100%; overflow: hidden;border-radius: 10px;">
            <div class="video-box" style="position: relative">
                <div data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 16px; z-index: 99;">
                    <img class="cursor-pointer" src="<?= Yii::getAlias('@web') ?>/img/dashboard/close-square-2.svg" alt="close modal button" width="36">
                </div>
                <div id="video">
                    <iframe id="teacher-iframe" width="100%" height="100%"
                            src=""
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                </div>
            </div>
            <div style="padding: 24px 24px 20px 24px; border-bottom: 1px solid lightgray">
                <div data-parse="name" style="color: #1a1c1f;font-weight: 600;font-size: 27px;line-height: 36px;">

                </div>
                <div data-parse="country" style="margin-top: 4px;color: #1a1c1f;font-weight: 500;font-size: 15px;line-height: 24px;">

                </div>
                <div style="margin-top: 24px;color: #373e45;font-weight: 400;font-size: 15px;line-height: 24px;">
                    <img src="<?= Yii::getAlias('@web') ?>/img/dashboard/teacher.svg" alt="teacher icon" width="24" style="margin-right: 12px">
                    <?= Yii::$app->devSet->getTranslate('experience') ?>: <span data-parse="experience"></span> <?= Yii::$app->devSet->getTranslate('years') ?>
                </div>
            </div>
            <div style="height: auto; padding: 10px 24px 28px 24px; overflow: scroll">
                <div data-parse="description" style="line-height: 24px; font-size: 15px;color: #5c646c;">

                </div>
            </div>
        </div>
    </div>
</div>
