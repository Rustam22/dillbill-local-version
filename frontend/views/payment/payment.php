<?php

use backend\models\Packets;
use yii\helpers\Url;

$currency = 'usd';
$currencyIcon = 'USD';
$multiplier = 1;
$USD = 1;
$ipCountry = Yii::$app->devSet->ip_info("Visitor", "Country");
$lang = Yii::$app->language;

//$ipCountry = 'Turkey';
//echo $lang;

if ($ipCountry == 'Azerbaijan') {
    $currency = 'azn';
    $currencyIcon = 'AZN';
} elseif ($ipCountry == 'Turkey') {
    $currency = 'try';
    $currencyIcon = 'TL';
} elseif ($ipCountry == 'Brazil') {
    $currency = 'brl';
    $currencyIcon = 'BRL';
}

$packets = Packets::find()->where(['id' => [2, 3, 4]])->asArray()->all();
$trial = Packets::findOne(['id' => 1]);
$userTimeZone = (Yii::$app->user->identity->userProfile->timezone == null) ? 'Asia/Baku' : Yii::$app->user->identity->userProfile->timezone;
$userLevel = Yii::$app->user->identity->userParameters->currentLevel;
//debug(Yii::$app->controller->id);

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


?>

<link href="/css/payment/payment.css" rel="stylesheet">

<?php if (Yii::$app->controller->id == 'payment') { ?>
    <script>
        $(document).ready(function () {
            let packetId = null
            let max = 0
            let selected = 0
            let weekdays = null
            let timeRange = null
            let userLevel = '<?= $userLevel ?>'
            let _generateCheckout = '<?= Url::to(['payment/generate-checkout'], true) ?>'

            $('.start-trial').click(function () {
                if (userLevel !== 'empty') {
                    max = $(this).data('lessons-count')
                    packetId = $(this).data('packet-id')

                    /*  Refresh fields  */
                    $('selected').html(0)
                    $('input[name="weekday"]').prop('disabled', false).prop('checked', false)
                    $('input[name="weekday"] + label').removeClass('disabled-visually')
                    $('.time-ranges .form-check-input').prop('checked', false)

                    $('max').html(max)
                    $('#slot').click()
                }
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

                //console.log(weekdays + ' - ' + timeRange + ' - ' + packetId)

                $.ajax({
                    url : _generateCheckout,
                    type : 'POST',
                    data : {
                        '_csrf-frontend': _csrf_frontend,
                        'PRICE_ID': packetId,
                        'LESSON_ID': '',
                        'weekdays': weekdays,
                        'timeRange': timeRange,
                    },
                    async: false,
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
                            let redirectUrl = '<?= Url::to(['payment/checkout'], true) ?>?token=' + data.token

                            $('.alert-danger').hide(0)

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
                        $('#approve-schedule .spinner-grow').addClass('display-none')
                    }
                });
            })

        })
    </script>
<?php } ?>


<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

<div class="top-space"></div>
<div id="packets" class="container" style="max-width: 829px;">
    <div class="row">
        <div class="col-12" align="center">
            <h1>
                <?= Yii::$app->devSet->getTranslate('selectPlan') ?>
            </h1>
        </div>
    </div>

    <div class="row bullet-points justify-content-between">
        <div class="col-sm-4 text-center">
            <span class="bullet-info">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 2C6.977 2 2.5 6.477 2.5 12C2.5 17.523 6.977 22 12.5 22C18.023 22 22.5 17.523 22.5 12C22.5 6.477 18.023 2 12.5 2ZM18.207 9.707L11.207 16.707C11.012 16.902 10.756 17 10.5 17C10.244 17 9.988 16.902 9.793 16.707L6.793 13.707C6.402 13.316 6.402 12.684 6.793 12.293C7.184 11.902 7.816 11.902 8.207 12.293L10.5 14.586L16.793 8.293C17.184 7.902 17.816 7.902 18.207 8.293C18.598 8.684 18.598 9.316 18.207 9.707Z" fill="#4FAE33"/>
                </svg>
                <?= Yii::$app->devSet->getTranslate('bullet1') ?>
            </span>
        </div>
        <div class="col-sm-4 text-center">
            <span class="bullet-info">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 2C6.977 2 2.5 6.477 2.5 12C2.5 17.523 6.977 22 12.5 22C18.023 22 22.5 17.523 22.5 12C22.5 6.477 18.023 2 12.5 2ZM18.207 9.707L11.207 16.707C11.012 16.902 10.756 17 10.5 17C10.244 17 9.988 16.902 9.793 16.707L6.793 13.707C6.402 13.316 6.402 12.684 6.793 12.293C7.184 11.902 7.816 11.902 8.207 12.293L10.5 14.586L16.793 8.293C17.184 7.902 17.816 7.902 18.207 8.293C18.598 8.684 18.598 9.316 18.207 9.707Z" fill="#4FAE33"/>
                </svg>
                <?= Yii::$app->devSet->getTranslate('bullet2') ?>
            </span>
        </div>
        <div class="col-sm-4 text-center">
            <span class="bullet-info">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 2C6.977 2 2.5 6.477 2.5 12C2.5 17.523 6.977 22 12.5 22C18.023 22 22.5 17.523 22.5 12C22.5 6.477 18.023 2 12.5 2ZM18.207 9.707L11.207 16.707C11.012 16.902 10.756 17 10.5 17C10.244 17 9.988 16.902 9.793 16.707L6.793 13.707C6.402 13.316 6.402 12.684 6.793 12.293C7.184 11.902 7.816 11.902 8.207 12.293L10.5 14.586L16.793 8.293C17.184 7.902 17.816 7.902 18.207 8.293C18.598 8.684 18.598 9.316 18.207 9.707Z" fill="#4FAE33"/>
                </svg>
                <?= Yii::$app->devSet->getTranslate('bullet3') ?> - <?= $trial[$currency] ?> <?= $currencyIcon ?>
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="packet-card">
                <h2>
                    <?= Yii::$app->devSet->getTranslate($packets[0]['nameKeyword']) ?>
                </h2>
                <div class="price-block display-flex">
                    <div>
                        <?php if ($packets[0]['discountPercent'] > 0) { ?>
                            <discount class="position-relative">
                                <div class="red-stripe position-absolute w-100"></div>
                                <?= $packets[0][$currency] ?><?= $currencyIcon ?>
                            </discount><br>
                        <?php } ?>

                        <price><?= round($packets[0][$currency] - ($packets[0][$currency] * $packets[0]['discountPercent']) / 100) ?></price><currency><?= $currencyIcon ?></currency>
                        <br>
                        <span><?= Yii::$app->devSet->getTranslate('monthlyL') ?></span>
                    </div>
                    <img src="/img/payment/walking.svg" alt="walking icon">
                </div>

                <div style="margin-bottom: 25px;border-top: 1px solid #D9DFE9;"></div>

                <div class="description-block">
                    <?= Yii::$app->devSet->getTranslate($packets[0]['descriptionKeyword']) ?>
                </div>

                <?php if (Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
                    <a href="<?= Url::to(['dashboard/my-classes'], true) ?>">
                        <button type="button" class="btn start-trial">
                            <?= Yii::$app->devSet->getTranslate('startTrial') ?>
                        </button>
                    </a>
                <?php } else { ?>
                    <button type="button" class="btn start-trial" data-lessons-count="<?= $packets[0]['lesson'] ?>" data-packet-id="<?= $packets[0]['id'] ?>">
                        <?= Yii::$app->devSet->getTranslate('subscribe') ?>
                    </button>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="packet-card position-relative popular">
                <div class="popular-badge position-absolute">
                    <?= Yii::$app->devSet->getTranslate('mostPopular') ?>
                </div>

                <h2>
                    <?= Yii::$app->devSet->getTranslate($packets[1]['nameKeyword']) ?>
                </h2>
                <div class="price-block display-flex">
                    <div>
                        <?php if ($packets[1]['discountPercent'] > 0) { ?>
                            <discount class="position-relative">
                                <div class="red-stripe position-absolute w-100"></div>
                                <?= $packets[1][$currency] ?><?= $currencyIcon ?>
                            </discount><br>
                        <?php } ?>

                        <price><?= round($packets[1][$currency] - ($packets[1][$currency] * $packets[0]['discountPercent']) / 100) ?></price><currency><?= $currencyIcon ?></currency>
                        <br>
                        <span><?= Yii::$app->devSet->getTranslate('monthlyL') ?></span>
                    </div>
                    <img src="/img/payment/running.svg" alt="running icon">
                </div>

                <div style="margin-bottom: 25px;border-top: 1px solid #D9DFE9;"></div>

                <div class="description-block">
                    <?= Yii::$app->devSet->getTranslate($packets[1]['descriptionKeyword']) ?>
                </div>

                <?php if (Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
                    <a href="<?= Url::to(['dashboard/my-classes'], true) ?>" class="start-trial">
                        <button type="button" class="btn start-trial">
                            <?= Yii::$app->devSet->getTranslate('startTrial') ?>
                        </button>
                    </a>
                <?php } else { ?>
                    <button type="button" class="btn start-trial" data-lessons-count="<?= $packets[1]['lesson'] ?>" data-packet-id="<?= $packets[1]['id'] ?>">
                        <?= Yii::$app->devSet->getTranslate('subscribe') ?>
                    </button>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="packet-card">
                <h2>
                    <?= Yii::$app->devSet->getTranslate($packets[2]['nameKeyword']) ?>
                </h2>
                <div class="price-block display-flex">
                    <div>
                        <?php if ($packets[2]['discountPercent'] > 0) { ?>
                            <discount class="position-relative">
                                <div class="red-stripe position-absolute w-100"></div>
                                <?= $packets[2][$currency] ?><?= $currencyIcon ?>
                            </discount><br>
                        <?php } ?>

                        <price><?= round($packets[2][$currency] - ($packets[2][$currency] * $packets[2]['discountPercent']) / 100) ?></price><currency><?= $currencyIcon ?></currency>
                        <br>
                        <span><?= Yii::$app->devSet->getTranslate('monthlyL') ?></span>
                    </div>
                    <img src="/img/payment/rushing.svg" alt="rushing icon">
                </div>

                <div style="margin-bottom: 25px;border-top: 1px solid #D9DFE9;"></div>

                <div class="description-block">
                    <?= Yii::$app->devSet->getTranslate($packets[2]['descriptionKeyword']) ?>
                </div>

                <?php if (Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
                    <a href="<?= Url::to(['dashboard/my-classes'], true) ?>" class="start-trial">
                        <button type="button" class="btn start-trial">
                            <?= Yii::$app->devSet->getTranslate('startTrial') ?>
                        </button>
                    </a>
                <?php } else { ?>
                    <button type="button" class="btn start-trial" data-lessons-count="<?= $packets[2]['lesson'] ?>" data-packet-id="<?= $packets[2]['id'] ?>">
                        <?= Yii::$app->devSet->getTranslate('subscribe') ?>
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="bottom-space"></div>

<button type="button" id="slot" class="display-none" data-bs-toggle="modal" data-bs-target="#class-response">ok</button>
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

            <div class="modal-body">
                <h4><?= Yii::$app->devSet->getTranslate('setYourSchedule') ?></h4>

                <div style="margin-top: 42px"></div>

                <div class="weekdays-block">
                    <div class="weekdays-description display-flex justify-content-between">
                        <button class="btn no-style" data-toggle="tooltip" data-placement="top" title="<?= Yii::$app->devSet->getTranslate('tooltipWeekdays') ?>">
                            <h5 class="cursor-pointer">
                                <?= Yii::$app->devSet->getTranslate('selectTheDaysOfTheWeek') ?>:
                                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.99967 1.33398C4.32567 1.33398 1.33301 4.32665 1.33301 8.00065C1.33301 11.6747 4.32567 14.6673 7.99967 14.6673C11.6737 14.6673 14.6663 11.6747 14.6663 8.00065C14.6663 4.32665 11.6737 1.33398 7.99967 1.33398ZM7.99967 2.66732C10.9531 2.66732 13.333 5.04724 13.333 8.00065C13.333 10.9541 10.9531 13.334 7.99967 13.334C5.04626 13.334 2.66634 10.9541 2.66634 8.00065C2.66634 5.04724 5.04626 2.66732 7.99967 2.66732ZM7.33301 4.66732V6.00065H8.66634V4.66732H7.33301ZM7.33301 7.33398V11.334H8.66634V7.33398H7.33301Z" fill="#3CA9F8"/>
                                </svg>
                            </h5>
                        </button>
                        <div class="counter">
                            <selected>0</selected>/<max>0</max>
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
                        <button class="btn no-style" data-toggle="tooltip" data-placement="top" title="<?= Yii::$app->devSet->getTranslate('tooltipSelectTimeRange') ?>">
                            <h5 class="cursor-pointer">
                                <?= Yii::$app->devSet->getTranslate('selectTimeRange') ?>:
                                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.99967 1.33398C4.32567 1.33398 1.33301 4.32665 1.33301 8.00065C1.33301 11.6747 4.32567 14.6673 7.99967 14.6673C11.6737 14.6673 14.6663 11.6747 14.6663 8.00065C14.6663 4.32665 11.6737 1.33398 7.99967 1.33398ZM7.99967 2.66732C10.9531 2.66732 13.333 5.04724 13.333 8.00065C13.333 10.9541 10.9531 13.334 7.99967 13.334C5.04626 13.334 2.66634 10.9541 2.66634 8.00065C2.66634 5.04724 5.04626 2.66732 7.99967 2.66732ZM7.33301 4.66732V6.00065H8.66634V4.66732H7.33301ZM7.33301 7.33398V11.334H8.66634V7.33398H7.33301Z" fill="#3CA9F8"/>
                                </svg>
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
                            <?= Yii::$app->devSet->getTranslate('goToPayment') ?>&nbsp;
                            <div class="spinner-grow text-light display-none" role="status" style="width: 16px;height: 16px;"></div>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

