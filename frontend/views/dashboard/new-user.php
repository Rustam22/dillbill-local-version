<?php

use backend\models\Packets;
use backend\models\TrialConversation;
use backend\models\TrialConversationUsers;
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

$anonymousTutor = 87;

$levels = [
    //'beginner' =>           ['name' => 'Beginner', 'segment' => 'A1'],
    'elementary' =>         ['name' => 'Elementary', 'segment' => 'A12'],
    'pre-intermediate' =>   ['name' => 'Pre-Intermediate', 'segment' => 'B1.1'],
    'intermediate' =>       ['name' => 'Intermediate', 'segment' => 'B1.2'],
    'upper-intermediate' => ['name' => 'Upper-Intermediate', 'segment' => 'B2'],
];

if (!isset($_COOKIE['class-tab'])) {
    $_COOKIE['class-tab'] = 'available-classes';
}

$fromDate = ($localDateTime->format('d') > $userDateTime->format('d')) ? $localDateTime : $userDateTime;
$classes = null;

$availableDates = [];

$reservedClassExists = TrialConversationUsers::find()->where(['userId' => Yii::$app->user->id])->exists();
$reservedClassLevel = TrialConversationUsers::findOne(['userId' => Yii::$app->user->id]);

$upcomingDates = TrialConversation::find()->
select(["date, CASE WHEN `date` = '".$localDateTime->format('Y-m-d')."' THEN(CASE WHEN `startsAt` < '".$localDateTime->format('H:i')."' THEN 0 ELSE 1 END) ELSE 1 END AS `bool`"])->
distinct()->
andWhere(['>=', 'date', $fromDate->format('Y-m-d')])->
having(['=', 'bool', 1])->
orderBy(['date' => SORT_ASC])->
asArray()->
all();


$upComingDate = ($upcomingDates == null) ? new DateTime() : new DateTime($upcomingDates[0]['date']);


foreach ($upcomingDates as $key => $value) {
    $currentDate = new DateTime($value['date']);
    if (str_contains($userSchedule, $currentDate->format('w'))) {
        $availableDates[] = $value['date'];
    }
}

//debug($availableDates);

if(!isset($_COOKIE['selected-date'])) {
    $_COOKIE['selected-date'] = ($reservedClassExists) ? 'pre-intermediate' : 'beginner';
}

if(!isset($_COOKIE['enterRoomInformed'])) {
    $_COOKIE['enterRoomInformed'] = 'no';
}


$_COOKIE['selected-date'] = (!isset($levels[$_COOKIE['selected-date']])) ? 'beginner' : $_COOKIE['selected-date'];

$_COOKIE['selected-date'] = (Yii::$app->user->identity->userProfile->preliminaryLevel != null) ? Yii::$app->user->identity->userProfile->preliminaryLevel : $_COOKIE['selected-date'];

$_COOKIE['selected-date'] = ($reservedClassLevel != null) ? $reservedClassLevel->conversationLevel : $_COOKIE['selected-date'];


$lang = ucfirst(Yii::$app->language);
$currency = 'usd';
$currencyIcon = 'USD';
$price = 1;
$ipCountry = Yii::$app->devSet->ip_info("Visitor", "Country");

$packet = Packets::findOne(['id' => 1]);

//$ipCountry = 'Turkey';

if ($ipCountry == 'Azerbaijan') {
    $currency = 'azn';
    $currencyIcon = 'AZN';
} elseif ($ipCountry == 'Turkey') {
    $currency = 'try';
    $currencyIcon = 'TL';
    $price = 15;
} elseif ($ipCountry == 'Brazil') {
    $currency = 'brl';
    $currencyIcon = 'BRL';
    $price = 5;
}

?>


<link href="<?=Yii::getAlias('@web');?>/css/dashboard/class-card.css" rel="stylesheet">
<link href="<?=Yii::getAlias('@web');?>/css/dashboard/study-banner.css" rel="stylesheet">
<link href="<?=Yii::getAlias('@web');?>/css/dashboard/my-classes.css" rel="stylesheet">

<script>
    let _currentClassDate = '<?= $_COOKIE['selected-date'] ?>'
    let _userDateTime = '<?= $userDateTime->format('M d, Y') ?>'
    let _userStartTime = '<?= $userStartTime ?>'
    let _userEndTime = '<?= $userEndTime ?>'
    let _startClassDate = false
    let _generateCheckout = '<?= Url::to(['payment/generate-checkout'], true) ?>'
    let _trialPriceId = 1
    let _selectedTrialLessonId = null
</script>
<script>
    $(document).ready(function () {
        $('.reserve-room-button').click(function (event) {
            _selectedTrialLessonId = $(this).closest('.class-column').data('class-id')

            if(_selectedTrialLessonId === null) {
                alert('Class is not selected')
                return false
            }

            $.ajax({
                url : _generateCheckout,
                type : 'POST',
                data : {
                    '_csrf-frontend': _csrf_frontend,
                    'PRICE_ID': _trialPriceId,
                    'LESSON_ID': _selectedTrialLessonId,
                    'weekdays': '',
                    'timeRange': ''
                },
                async: false,
                beforeSend: function() {

                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)

                    if(!data.success) {
                        alert(data.error)
                    } else {
                        let redirectUrl = '<?= Url::to(['payment/checkout'], true) ?>?token=' + data.token

                        window.location = redirectUrl
                        window.location.replace(redirectUrl)
                        window.location.href = redirectUrl

                        let testTimerID = window.setTimeout(function() {
                            window.location.href = redirectUrl
                        }, 3*250)
                    }
                },
                error : function(request, error) {
                    console.log(error);
                    console.log(request);
                    alert(request.statusText);
                },
                complete: function() {

                }
            });

        })
    })
</script>

<style>
    .reserve-room-button {
        font-weight: 500 !important;
        background-color: #1877F2;
        color: white;
    }
    ol {
        text-align: left;
        margin-top: 10px;
        color: #2D2D2D;
        font-size: 16px;
        line-height: 22px;
        margin-right: 15px;
    }
    ol li {
        margin-top: 25px;
    }
    @media (max-width: 410px) {
        .modal-body {
            padding: 15px !important;
        }
        ol {
            margin-right: 10px !important;
            margin-left: 0 !important;
        }
    }
</style>


<?php if ($reservedClassExists) { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/reconnecting-websocket/1.0.0/reconnecting-websocket.min.js"></script>
    <script>
        $(document).ready(function () {
            let _socketUrl = '<?= (Yii::$app->devSet->isLocal()) ? 'ws://localhost:8880' : 'wss://dillbill.com/ws' ?>'
            let conversation =  new WebSocket(_socketUrl)
            let _socketApiKey = '<?= sha1(Yii::$app->devSet->getDevSet('socketApiKey')) ?>'
            let _enterBefore = parseInt('<?= Yii::$app->devSet->getDevSet('enterBefore') ?>', 10)
            let _token = '<?= Yii::$app->devSet->myEncryption(Yii::$app->user->identity->verification_token, Yii::$app->devSet->getDevSet('socketApiKey')) ?>'
            let _userTimeZone = '<?= $userTimeZone ?>'
            let _localTimeZone = '<?= Yii::$app->timeZone ?>'
            let _confirmPhoneNumber = '<?=  Url::to(['dashboard/confirm-phone-number'], true); ?>'

            Date.prototype.getDateTimeByTimeZone = function (timeZone) {
                let date = new Date(new Date().toLocaleString("en-US", {timeZone: timeZone}));
                this.setTime(date.getTime());

                return this;
            }

            function getCurrentLocalDateTime() {
                return new Date().getDateTimeByTimeZone(_localTimeZone)
            }

            function getCurrentUserDateTime() {
                return new Date().getDateTimeByTimeZone(_userTimeZone)
            }

            function convertHMS(second) {
                const sec = parseInt(second, 10)
                let hours  = Math.floor(sec / 3600)
                let minutes = Math.floor((sec - (hours * 3600)) / 60)
                let seconds = sec - (hours * 3600) - (minutes * 60)

                if (hours < 10) {hours = "0" + hours}
                if (minutes < 10) {minutes = "0" + minutes}
                if (seconds < 10) {seconds = "0" + seconds}

                return {hour: hours, minute: minutes, second: seconds}
            }

            //let showEnter = true

            function countDownTimer() {
                $('.reserved').each(function(e) {
                    let classLocalDateTime = new Date($(this).data('class-start-date-time'))
                    let timeLeft = classLocalDateTime.getTime() - getCurrentLocalDateTime().getTime() - (_enterBefore * 60 * 1000)
                    timeLeft = (timeLeft < 0) ? 0 : timeLeft
                    timeLeft /= 1000

                    $(this).find('.hours').html(convertHMS(timeLeft).hour)
                    $(this).find('.minutes').html(convertHMS(timeLeft).minute)
                    $(this).find('.seconds').html(convertHMS(timeLeft).second)

                    let chosenClass = $(this).closest('.class-column')
                    let classLocalStartDateTime = new Date(chosenClass.data('class-start-date-time'))

                    /*if ( showEnter && (getCurrentLocalDateTime().getTime() >= (classLocalStartDateTime.getTime() - (_enterBefore * 60 * 1000))) )  {
                        showEnter = false
                    }*/
                })
            }
            countDownTimer()

            function enableAfterCancel(classDate) {
                $('[data-class-date="' + classDate + '"]:not(.reserved) .reserve-room-button').removeClass('disabled-visually')
            }

            function disableAfterReserve() {
                $('.reserved').each(function (e) {
                    let classDate = $(this).data('class-date')
                    $('[data-class-date="' + classDate + '"]:not(.reserved) .reserve-room-button').addClass('disabled-visually')
                })
            }
            disableAfterReserve()

            setTimeout(function run() {
                countDownTimer()
                setTimeout(run, 1000);
            }, 1000);

            conversation = new ReconnectingWebSocket(_socketUrl)

            conversation.onopen = function(e) {
                conversation.send(JSON.stringify({
                    'socketApiKey': _socketApiKey,
                    'token': _token,
                    'action': 'addToSocket'
                }))
            }

            conversation.onmessage = function(e) {
                let serverResponse = JSON.parse(e.data)
                console.log(serverResponse)

                if(serverResponse.success === true) {
                    if(serverResponse.action === 'reserve') {
                        let selector = $('.class-column[data-class-id="' + serverResponse.reservedConversationId + '"]')

                        // If response belongs to me
                        if(_token === serverResponse.referralToken) {
                            selector.addClass('reserved')
                            selector.find('.my-spinner').addClass('display-none')
                            selector.find('.reserve-room-button').stop().slideUp(0)
                            selector.find('.enter-room-section').fadeIn(700)
                        }

                        disableAfterReserve()

                        // Belongs to everybody
                        let attendee = '<div class="attendee up-down-shake" data-user-id="' + serverResponse.userId + '" style="background-color: ' + serverResponse.color + ';">' + serverResponse.bigLetter + '</div>'

                        selector.find('.waiting-attendee:first').fadeOut(0, function () {
                            $(this).replaceWith(attendee)
                        })
                    }

                    if(serverResponse.action === 'trialEnterRoom') {
                        let selector = $('.class-column[data-class-id="' + serverResponse.enteredClassId + '"]')
                        selector.find('.my-spinner').addClass('display-none')

                        window.location = serverResponse.zoom
                        window.location.replace(serverResponse.zoom)
                        window.location.href = serverResponse.zoom

                        let testTimerID = window.setTimeout(function() {
                            window.location.href = serverResponse.zoom
                        }, 3*250 )
                    }

                    if(serverResponse.action === 'cancel') {
                        let selector = $('.class-column[data-class-id="' + serverResponse.canceledConversationId + '"]')
                        selector.find('.my-spinner').addClass('display-none')

                        // If response belongs to me
                        if(_token === serverResponse.referralToken) {
                            selector.removeClass('reserved')
                            selector.find('.my-spinner').addClass('display-none')
                            selector.find('.enter-room-section').stop().slideUp(0, function () {
                                selector.find('.reserve-class').removeClass('display-none')
                                selector.find('.reserve-room-button').fadeIn(700)
                            })
                        }

                        // Belongs to everybody
                        let emptyAttendee = '<div class="waiting-attendee"><img src="/img/dashboard/add-profile.svg" alt=""></div>'
                        let cancelledUser = selector.find('.attendee[data-user-id="' + serverResponse.userId + '"]')

                        cancelledUser.fadeOut(700)
                        setTimeout(() => {cancelledUser.replaceWith(emptyAttendee)}, 600)
                        enableAfterCancel(selector.data('class-date'))
                    }
                }

                if(serverResponse.success === false) {
                    $('.my-spinner').addClass('display-none')

                    if(serverResponse.error === 'balance') {
                        $('[data-bs-target="#class-response"]').click()
                    } else {
                        alert(serverResponse['error-message'])
                    }
                }
            }

            $('.enter-room-button').click(function () {
                let chosenClass = $(this).closest('.class-column')
                let classLocalStartDateTime = new Date(chosenClass.data('class-start-date-time'))

                if(getCurrentLocalDateTime().getTime() < (classLocalStartDateTime.getTime() - (_enterBefore * 60 * 1000))) {
                    chosenClass.find('.informative-section').addClass('date-time-shake')
                    setTimeout(() => { chosenClass.find('.informative-section').removeClass('date-time-shake') }, 4200)
                } else {
                    $(this).find('.my-spinner').removeClass('display-none')

                    conversation.send(JSON.stringify({
                        'socketApiKey': _socketApiKey,
                        'token': _token,
                        'action': 'trialEnterRoom',
                        'conversation-id': chosenClass.data('class-id')
                    }))
                }
            })



            setTimeout(function () { $('#pre-boarding-trigger').click() }, 1000)

            $('#pre-boarding-carousel .next-to-google-calendar').click(function () {
                let phoneNumber = $('.google-calendar-input[name="phone-number"]').val().trim()

                if (phoneNumber.length < 7 || phoneNumber.length > 20) {
                    $('#pre-boarding-carousel .wrong-phone').fadeIn(400)
                    return false
                } else {
                    $.ajax({
                        url : _confirmPhoneNumber,
                        type : 'POST',
                        async: false,
                        data : {
                            '_csrf-frontend': _csrf_frontend,
                            'phoneNumber': phoneNumber
                        },
                        beforeSend: function() {
                            $('.next-to-google-calendar .spinner-grow').removeClass('display-none')
                        },
                        success : function(data) {
                            data = JSON.parse(data)
                            console.log(data)

                            if(!data.success) {
                                alert(data.error)
                            } else {
                                $('#pre-boarding-carousel .wrong-phone').hide(0)
                                $('#pre-boarding-carousel').carousel('next')
                            }
                        },
                        error : function(request, error) {
                            alert('System error')
                            console.log('error')
                        },
                        complete: function() {
                            $('.next-to-google-calendar .spinner-grow').addClass('display-none')
                        }
                    })
                }
            })

            $('#enter-room-info-check').click(function () {
                if ($('#enter-room-confirm .primary-button').prop('disabled')) {
                    $('#enter-room-confirm .primary-button').prop('disabled', false).removeClass('disabled-visually')
                } else {
                    $('#enter-room-confirm .primary-button').prop('disabled', true).addClass('disabled-visually')
                }
            })
            $('#enter-room-confirm .primary-button').click(function () {
                document.cookie = "enterRoomInformed=yes"
                $('#enter-room-confirm .btn-close').click()
            })

            <?php if ((Yii::$app->user->identity->userProfile->phone) != null AND ($_COOKIE['enterRoomInformed'] != "yes")) { ?>
                $('button[data-bs-target="#enter-room-confirm"]').click()
            <?php } ?>

        })
    </script>

    <?php if (Yii::$app->user->identity->userProfile->phone == null) { ?>
        <button type="button" id="pre-boarding-trigger" class="display-none" data-bs-toggle="modal" data-bs-target="#boarding">ok</button>
        <div class="modal fade response-modal show level-time-range-select" id="boarding" style="background-color: rgba(0, 0, 0, 0.52);" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body" style="padding: 25px 30px;">
                        <div id="pre-boarding-carousel" class="carousel slide" data-bs-interval="false">
                            <div class="carousel-inner">

                                <div class="carousel-item active">
                                    <div class="modal-footer display-flex justify-content-center" style="padding: 0;">
                                        <img src="/img/dashboard/phone-number.svg" alt="">
                                        <div style="margin-top:10px;"></div>
                                        <p style="color: #202734;font-weight: 500;font-size: 22px;margin-bottom: 8px;">
                                            <?= Yii::$app->devSet->getTranslate('enterContactNumber') ?>
                                        </p>
                                        <span style="color: #646E82;text-align: center;font-weight: 400;font-size: 15px;">
                                            <?= Yii::$app->devSet->getTranslate('weNeedYourContactNumber') ?>
                                        </span>
                                        <input name="phone-number"
                                               class="google-calendar-input"
                                               style="height: 48px;border: 1px solid #E0E2E7;border-radius: 8px;margin-bottom: 15px;margin-top: 15px;"
                                               placeholder="<?= Yii::$app->devSet->getTranslate('phoneNumber') ?>"
                                               value="<?php if (Yii::$app->user->identity->userProfile->phone != null) { ?> <?= Yii::$app->user->identity->userProfile->phone; ?> <?php } ?>">
                                        <code class="wrong-phone display-none" style="text-align: left; margin-top: -10px;">
                                            <?= Yii::$app->devSet->getTranslate('wrongPhoneNumber') ?>
                                        </code>
                                    </div>

                                    <div class="modal-footer" style="padding: 0;">
                                        <div style="margin-top:5px;"></div>
                                        <button type="button" class="btn primary-button next-to-google-calendar w-100">
                                            <?= Yii::$app->devSet->getTranslate('next') ?>
                                            <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="carousel-item" id="trial-google-calendar">
                                    <div class="modal-footer display-flex justify-content-center" style="padding: 0;">
                                        <img alt="" src="<?=Yii::getAlias('@web');?>/img/dashboard/google-calendar-connect.svg"
                                             style="margin-top:10px;margin-bottom: 15px;">
                                        <h2 style="color: #202734;font-size: 22px;font-weight: 600;">
                                            <?= Yii::$app->devSet->getTranslate('connectGoogleCalendar') ?>
                                        </h2>
                                        <div style="margin-bottom: 10px;"></div>
                                        <span style="color: #646E82; font-size: 16px;display: block;font-weight: 400;text-align: center;">
                                            <?= Yii::$app->devSet->getTranslate('byConnectingYourGoogleCalendar') ?>
                                        </span>

                                        <input class="google-calendar-input" style="margin-top: 10px;"
                                               placeholder="example@gmail.com"
                                               value="<?php if(Yii::$app->user->identity->userParameters->calendarGmail == null) { ?><?= Yii::$app->user->identity->email ?><?php } else { ?><?= Yii::$app->user->identity->userParameters->calendarGmail ?><?php } ?>">
                                        <code class="display-none">
                                            &nbsp;<?= Yii::$app->devSet->getTranslate('useGmail') ?>.
                                        </code>
                                        <span style="color: #646E82;font-size: 13px;text-align: center;">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                            <path d="M8.00016 14.6663C9.8411 14.6663 11.5078 13.9201 12.7142 12.7137C13.9206 11.5073 14.6668 9.84061 14.6668 7.99967C14.6668 6.15874 13.9206 4.49207 12.7142 3.28563C11.5078 2.0792 9.8411 1.33301 8.00016 1.33301C6.15923 1.33301 4.49256 2.0792 3.28612 3.28563C2.07969 4.49207 1.3335 6.15874 1.3335 7.99967C1.3335 9.84061 2.07969 11.5073 3.28612 12.7137C4.49256 13.9201 6.15923 14.6663 8.00016 14.6663Z" stroke="#FBBC04" stroke-width="1.33" stroke-linejoin="round"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.99984 3.66699C8.46007 3.66699 8.83317 4.04009 8.83317 4.50033C8.83317 4.96056 8.46007 5.33366 7.99984 5.33366C7.5396 5.33366 7.1665 4.96056 7.1665 4.50033C7.1665 4.04009 7.5396 3.66699 7.99984 3.66699Z" fill="#FBBC04"/>
                                            <path d="M8.16667 11.3337V6.66699H7.83333H7.5" stroke="#FBBC04" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7 11.333H9.33333" stroke="#FBBC04" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>&nbsp;
                                            <?= Yii::$app->devSet->getTranslate('enterOnlyGmailAddress') ?>
                                        </span>
                                        <div style="margin-bottom: 15px;"></div>
                                    </div>

                                    <div class="modal-footer display-flex" style="padding: 0;flex-flow: row;">
                                        <button class="btn confirm-time w-100
                                            <?php if(Yii::$app->user->identity->userParameters->googleCalendar == 'yes') { ?>
                                                disconnect
                                            <?php } else { ?>
                                                connect
                                            <?php } ?>">
                                            <?php if(Yii::$app->user->identity->userParameters->googleCalendar == 'yes') { ?>
                                                <?= Yii::$app->devSet->getTranslate('disconnect') ?>
                                            <?php } else { ?>
                                                <?= Yii::$app->devSet->getTranslate('connect') ?>
                                            <?php } ?>
                                            <span class="spinner-grow spinner-grow-sm display-none" role="status" aria-hidden="true"></span>
                                        </button>

                                        <button type="button" class="btn close w-100" data-bs-dismiss="modal">
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
    <br>
<?php } ?>


<?php if (!$reservedClassExists) { ?>
<br>
<script>
    $(document).ready(function () {
        $('.choose-your-plan').click(function () {
            $('html, body').animate({
                scrollTop: $('.testimonial-classes').offset().top - 150
            }, 500, function () {
                $('.testimonial-classes').removeClass('date-time-shake')
                setTimeout(function () {
                    $('.testimonial-classes').addClass('date-time-shake')
                }, 200)
            })
        })
    })
</script>
<div class="welcome-back w-100">
    <div class="row">
        <div class="col-md-7 order-md-1 order-2">
            <h2 style="color: #646E82; font-weight: 400; font-size: 16px;">
                <?= Yii::$app->devSet->getTranslate('welcomeBack') ?>,
                <span style="color: #000000;font-weight: 600;"><?= Yii::$app->user->identity->userProfile->name ?>!</span> &nbsp;👋
            </h2>
            <h1 style="font-size: 24px;font-weight: 600;color:#000000;margin-top: 15px;">
                <?= Yii::$app->devSet->getTranslate('joinTrialLessonHeadline') ?> - <?= $packet[$currency] ?><?= $currencyIcon ?>
            </h1>
            <p style="color: #000000;font-size: 16px;font-weight: 400;margin-top: 10px;margin-bottom: 25px;">
                <?= Yii::$app->devSet->getTranslate('inTheTrialLessonDescription') ?>
            </p>
            <button class="btn choose-your-plan">
                <?= Yii::$app->devSet->getTranslate('joinTrialLessonHeadline') ?>
            </button>
        </div>
        <div class="col-md-5 order-md-2 order-1" align="center">
            <img class="w-100" alt="online lesson on zoom" src="/img/dashboard/choose-trial-lesson.png" style="max-width: 250px;">
        </div>
    </div>
</div>
<?php } ?>


<div class="tab-content" id="nav-tabContent">

    <!------------------  Available Classes  -------------------->
    <div class="tab-pane fade <?php if($_COOKIE['class-tab'] == 'available-classes') { ?> show active <?php } ?>"
         id="nav-home"
         role="tabpanel"
         aria-labelledby="nav-home-tab">

        <!-------------------- Study Banner -------------------->
        <?php if(Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
            <div class="today-topic display-flex position-relative">
                <div class="red-mark position-absolute">
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0V18L8 14.1561L16 18V0H0Z" fill="#FF3838"/>
                    </svg>
                </div>

                <div class="select-date dropdown position-relative <?php if ($reservedClassExists) { ?>display-none<?php } ?>" id="date-select">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton777" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="/img/dashboard/english-levels.svg" alt="" style="width: 23px;margin-right: 0;">
                        &nbsp;<span class="selected-date">
                            <?= $_COOKIE['selected-date'] ?>
                        </span>&nbsp;
                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.3335 1L5.3335 5L1.3335 1" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton777">
                        <?php foreach ($levels as $key => $value) { ?>
                            <li>
                                <a class="dropdown-item" data-class-date="<?= $key ?>">
                                    <svg class="display-none" width="11" height="9" viewBox="0 0 11 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.28033 4.77513C0.987437 4.48223 0.512563 4.48223 0.21967 4.77513C-0.0732233 5.06802 -0.0732233 5.54289 0.21967 5.83579L2.94202 8.55814C3.23491 8.85103 3.70979 8.85103 4.00268 8.55814L10.447 2.11383C10.7399 1.82093 10.7399 1.34606 10.447 1.05317C10.1541 0.760273 9.67923 0.760273 9.38634 1.05317L3.47235 6.96715L1.28033 4.77513Z" fill="#1676F3"/>
                                    </svg>
                                    <text><?= $value['name'] ?></text>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                    <div class="separator-line position-absolute"></div>
                </div>

                <div class="topic-group display-flex" <?php if ($reservedClassExists) { ?>style="margin-left: 0;"<?php } ?>>
                    <div>
                        <img class="" src="/img/dashboard/reading-day.svg" alt="" width="48">
                    </div>
                    <div style="margin-top: -8px;">
                        <span>
                            <?= Yii::$app->devSet->getTranslate('speakingTopic') ?>
                            &nbsp;
                            <?php if ($reservedClassExists) { ?>
                                <?= $levels[$_COOKIE['selected-date']]['segment'] ?> - <b style="color: #00B67A;"><?= $levels[$_COOKIE['selected-date']]['name'] ?></b>
                            <?php } ?>
                        </span>
                        <h5>
                            Travel
                        </h5>
                    </div>
                </div>

                <div class="buttons-side display-flex">
                    <div class="topic-group">
                        <a href="https://drive.google.com/file/d/1qNac7IlqnsllRiltsiPUAZ6Vi903ffna/view" class="w-100" target="_blank">
                            <div class="btn download-material">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"></path>
                                    <path d="M2 8.00277V14H14V8" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M11 7.6665L8 10.6665L5 7.6665" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M7.99707 2V10.6667" stroke="#1877F2" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                &nbsp;
                                <?= Yii::$app->devSet->getTranslate('download') ?>
                            </div>
                        </a>
                    </div>
                </div>


            </div>
        <?php } ?>

        <!-------------------- Class Cards -------------------->
        <?php if(Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
            <div class="testimonial-classes">
                <div class="classes-row row justify-content-lg-start justify-content-md-around">
                    <?php
                        if (!$reservedClassExists) {
                            $classes = TrialConversation::find()->
                            select(["*, CASE WHEN `date` = '".$localDateTime->format('Y-m-d')."' THEN(CASE WHEN `startsAt` < '".$localDateTime->format('H:i')."' THEN 0 ELSE 1 END) ELSE 1 END AS `bool`"])->
                            where(['date' => $availableDates])->
                            andWhere(['>=', 'date', $fromDate->format('Y-m-d')])->
                            having(['=', 'bool', 1])->
                            orderBy(['date' => SORT_ASC, 'startsAt' => SORT_ASC])->
                            all();
                        } else {
                            $classes = TrialConversation::find()->where(['id' => TrialConversationUsers::findOne(['userId' => Yii::$app->user->id])->trialConversationId])->all();
                        }
                    ?>
                    <?php foreach ($classes as $class) { ?>
                        <?php
                            $aboutClass = '';
                            $conversationUsers = $class->getTrialConversationUsers()->all();
                            $classReserveCount = 0;

                            foreach ($conversationUsers as $conversationUser) {
                                if($conversationUser->action == 'reserve' AND $conversationUser->user->id == Yii::$app->user->id) {
                                    $aboutClass = 'reserved';
                                    break;
                                }
                            }

                            foreach ($conversationUsers as $conversationUser) {
                                if($conversationUser->action == 'reserve' ) {
                                    $classReserveCount++;
                                }
                            }
                        ?>

                        <?php if($classReserveCount < (($reservedClassExists) ? (GROUP_SIZE + 1) : GROUP_SIZE) ) { ?>
                            <?php
                                $classDate = new DateTime($class->date.' '.$localDateTime->format('H:i'));
                                $classAlignedDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$class->startsAt), $userTimeZone);
                                $classAlignedStartDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$class->startsAt), $userTimeZone);
                                $classAlignedEndDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($class->date.' '.$class->endsAt), $userTimeZone);
                            ?>
                            <?php if($class->tutorId != null) { ?>
                                <div class="class-column col-lg-4 col-auto <?= $aboutClass ?>"
                                     data-class-id="<?= $class->id ?>"
                                     data-class-date="<?= $class->level ?>"
                                     data-start-time="<?= $class->startsAt ?>"
                                     data-end-time="<?= $class->endsAt ?>"
                                     data-class-start-date-time="<?= $classDate->format('M d, Y '.$class->startsAt) ?>"
                                     data-class-end-date-time="<?= $classDate->format('M d, Y '.$class->endsAt) ?>"

                                    <?php if($classDate->format('d.m.Y') != $_COOKIE['selected-date']) { ?> style="display: none;" <?php } ?>
                                >
                                    <div class="class-card">
                                        <section class="tutor-section">
                                            <div class="display-flex">
                                                <div class="story <?php if($class->tutor->presentation != null) { ?> clickable-story <?php } ?>"
                                                     data-name="<?php if($class->tutor->teacherName != null) { ?><?= $class->tutor->teacherName ?><?php } ?>"
                                                     data-country="<?php if($class->tutor->country != null) { ?><?= $class->tutor->country ?><?php } ?>"
                                                     data-experience="<?php if($class->tutor->experience != null) { ?><?= $class->tutor->experience ?><?php } ?>"
                                                     data-description="<?php if($class->tutor['description_'.Yii::$app->language] != null) { ?><?= $class->tutor['description_'.Yii::$app->language] ?><?php } ?>"
                                                     data-presentation="<?php if($class->tutor->presentation != null) { ?><?= $class->tutor->presentation ?><?php } ?>"
                                                    <?php if($class->tutor->presentation != null) { ?>
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal5"
                                                    <?php } ?>
                                                >
                                                    <img class="tutor-image" src="<?= Yii::$app->request->hostInfo ?>/backend/web/<?= $class->tutor->image ?>" alt="">
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
                                                        <h5><?= $class->tutor->teacherName ?></h5>
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
                                                    <?= Yii::$app->devSet->getTranslate('bookTrialLessons') ?>:
                                                    <br>
                                                    <b>&nbsp;<?= $price ?> <?= $currencyIcon ?></b>
                                                    <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                                </div>
                                            </section>

                                            <section class="enter-room-section <?php if($aboutClass != 'reserved') { ?> display-none <?php } ?>">
                                                <div class="display-flex">
                                                    <div class="btn enter-room-button" style="margin-right: 0;">
                                                        <?= Yii::$app->devSet->getTranslate('enterRoom') ?>
                                                        <span class="my-spinner display-none spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
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

</div>





<button type="button" class="display-none" data-bs-toggle="modal" data-bs-target="#enter-room-confirm"></button>
<div class="modal fade response-modal" id="enter-room-confirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding: 20px 25px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.77778 5.9979L11.6352 2.14181C12.1216 1.65561 12.1216 0.850856 11.6352 0.364652C11.1488 -0.121551 10.3438 -0.121551 9.85744 0.364652L6 4.22075L2.14256 0.364652C1.65618 -0.121551 0.851153 -0.121551 0.36478 0.364652C-0.121593 0.850856 -0.121593 1.65561 0.36478 2.14181L4.22222 5.9979L0.36478 9.854C-0.121593 10.3402 -0.121593 11.145 0.36478 11.6312C0.616352 11.8826 0.93501 12 1.25367 12C1.57233 12 1.89098 11.8826 2.14256 11.6312L6 7.77506L9.85744 11.6312C10.109 11.8826 10.4277 12 10.7463 12C11.065 12 11.3836 11.8826 11.6352 11.6312C12.1216 11.145 12.1216 10.3402 11.6352 9.854L7.77778 5.9979Z" fill="#646E82"/>
                    </svg>
                </button>
            </div>

            <div class="modal-body" style="padding: 15px 15px 0;" align="center">
                <img src="/img/dashboard/enter-room.svg" alt="enter room button">
                <h4>
                    <?= Yii::$app->devSet->getTranslate('thisIsHowYouWillEnter') ?>
                </h4>
                <?= Yii::$app->devSet->getTranslate('enterRoomRulesDescription') ?>
            </div>

            <div class="modal-footer" style="padding: 20px 14px;">
                <div class="row w-100">
                    <div class="col-12">
                        <label class="cursor-pointer" style="color: #646E82;font-size: 14px;">
                            <input class="form-check-input-available" type="checkbox" value="" id="enter-room-info-check">
                            <?= Yii::$app->devSet->getTranslate('iReadTheInformation') ?>
                        </label>
                        <div style="margin-top:5px;"></div>
                        <button type="button" class="btn primary-button w-100 disabled-visually" disabled>
                            <?= Yii::$app->devSet->getTranslate('okay') ?>
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


