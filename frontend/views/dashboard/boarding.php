<?php

use yii\helpers\Url;

$this->context->layout = false;

$userTimeZone = (Yii::$app->user->identity->userProfile->timezone == null) ? '' : Yii::$app->user->identity->userProfile->timezone;

?>

<head>
    <title>Boarding | DillBill</title>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-WWXHM4Z');</script>
    <!-- End Google Tag Manager -->

    <script>
        !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on","addSourceMiddleware","addIntegrationMiddleware","setAnonymousId","addDestinationMiddleware"];analytics.factory=function(e){return function(){var t=Array.prototype.slice.call(arguments);t.unshift(e);analytics.push(t);return analytics}};for(var e=0;e<analytics.methods.length;e++){var key=analytics.methods[e];analytics[key]=analytics.factory(key)}analytics.load=function(key,e){var t=document.createElement("script");t.type="text/javascript";t.async=!0;t.src="https://cdn.segment.com/analytics.js/v1/" + key + "/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);analytics._loadOptions=e};analytics._writeKey="0x8AxE1IIioq3mJgxEGDOKeuM8ewbV6Z";;analytics.SNIPPET_VERSION="4.15.3";
            analytics.load("0x8AxE1IIioq3mJgxEGDOKeuM8ewbV6Z");
            analytics.page();
        }}();
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?=Yii::getAlias('@web');?>/img/favicon.ico" type="image/ico">

    <!--------------- Bootstrap Css --------------->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!---------------Bootstrap js----------->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <style>@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap');</style>
</head>

<style>
    body {
        font-family: "Open Sans", serif;
    }
    .progress {
        border-radius: 8px;
        background-color: #E5E5E5;
    }
    .progress-bar {
        background: #58CC02;
        border-radius: 8px;
    }
    .progress {

    }
    .display-flex {
        display: flex;
    }
    @media only screen and (max-height: 685px) {
        .questions {
            justify-content: initial !important;
        }
    }
    h2 {
        color: #000000;
        font-weight: 600;
        font-size: 28px;
        text-align: center;
    }
    .form-check-input {
        display: none;
    }
    .source .form-check-label, .aim .form-check-label {
        padding: 15px 20px;
        //height: 58px;
        background: #FFFFFF;
        border: 1px solid #DFDFDF;
        border-radius: 12px;
        color: #4B4B4B;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 5px;
        cursor: pointer;
        width: 100%;
    }
    .source .form-check-label:hover,
    .aim .form-check-label:hover,
    .do-you-now-english .form-check-label:hover,
    .english-levels .form-check-label:hover {
        background: #F8F8F8;
    }
    .source .form-check-input:checked+label,
    .aim .form-check-input:checked+label,
    .do-you-now-english .form-check-input:checked+label,
    .english-levels .form-check-input:checked+label {
        background: #ECF8FF;
        border: 2px solid #6DBFFE;
        color: #1877F2;
    }
    .english-levels .form-check-input:checked+label h3, .do-you-now-english .form-check-input:checked+label h3 {
        color: #187FF2;
    }
    .form-check {
        padding-left: 0;
    }
    .questions {
        margin-top: 120px;
    }
    .carousel-item {
        transition: transform 0s ease-in-out;
    }
    .carousel-fade .active.carousel-item-start,
    .carousel-fade .active.carousel-item-end {
        transition: opacity 0s 0s;
    }
    .back {
        margin-top: -6px;
    }
    .display-none {
        display: none;
    }
    .do-you-now-english .form-check-label {
        border: 2px solid #DFDFDF;
        border-radius: 20px;
        padding: 24px;
        width: 100%;
        cursor: pointer;
        height: 247px;
    }
    .do-you-now-english .form-check-label h3 {
        color: #4B4B4B;
        font-weight: 600;
        font-size: 18px;
        line-height: 25px;
        margin-top: 20px;
    }
    .do-you-now-english .form-check-label p {
        color: #4B4B4B;
        font-weight: 300;
        font-size: 18px;
        line-height: 25px;
    }
    .do-you-now-english .form-check {
        max-width: 255px;
        width: 100%;
    }
    .english-levels .form-check-label h3 {
        color: #4B4B4B;
        font-weight: 600;
        font-size: 16px;
        line-height: 22px;
    }
    .english-levels .form-check-label p {
        font-weight: 300;
        font-size: 16px;
        line-height: 22px;
        color: #575757;
    }
    .english-levels .form-check-label {
        border: 1px solid #DFDFDF;
        border-radius: 12px;
        width: 100%;
        padding: 0 24px;
        margin-bottom: 10px;
        cursor: pointer;
    }
    .english-levels .form-check-label img {
        margin-right: 24px;
    }
    .english-levels .form-check-label .level-description {
        margin-top: 14px;
    }
    .time-zone .form-check-label {
        border: 1px solid #DFDFDF;
        border-radius: 12px;
        padding: 14px 24px;
    }
    #finish {
        background: #58CC02;
        border-radius: 12px;
        height: 60px;
        color: white;
        font-weight: 600;
        font-size: 16px;
    }
    .my-expire .confirm-time-zone {
        background-color: #2682FF;
        color: white;
    }
    .my-expire .confirm-time-zone:hover {
        border: 1px solid #2682FF;
        background-color: #2682ff0a;
        color: #2682FF;
    }
    @media (max-width: 576px) {      /* ___Mobile___ */
        .questions {
            margin-top: 25px !important;
        }
        .do-you-now-english .form-check:first-child {
            margin-bottom: 24px;
        }
        .do-you-now-english .display-flex {
            flex-direction: column !important;
            align-items: center !important;
        }
        .time-zone .form-check-label {
            padding: 10px 12px !important;
        }
        .time-zone span {
            font-size: 14px !important;
        }
        #timezone {
            font-size: 12px !important;
        }
    }
</style>

<script>
    let _userTimeZone = '<?= $userTimeZone ?>'
    let _browserTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone
    let _localTimeZone = '<?= Yii::$app->timeZone ?>'
    let _timeZoneAssigning = '<?= Url::to(['dashboard/time-zone-assign'], true) ?>'
    let _boarding = '<?= Url::to(['dashboard/boarding'], true) ?>'
    let _csrf_frontend = '<?= Yii::$app->request->csrfToken ?>'
    let chooseTimeZone = '<?= Yii::$app->devSet->getTranslate('chooseTimeZone') ?>'
    let UserCurrentTimeZone = '<?= Yii::$app->user->identity->userProfile->timezone ?>'
    let boarding = true
    let batch = {}

    $(document).ready(function () {
        let items = $('#carouselExampleFade .carousel-inner .carousel-item').length
        let progressPortion = 100 / (items - 1)
        let previousIndex = 0
        let nextIndex = 0
        let myCarousel = document.getElementById('carouselExampleFade')

        _userTimeZone = (_userTimeZone === '') ? _browserTimeZone : _userTimeZone
        $('#time-zone-name-gmt').html(_browserTimeZone)

        $('#time-zone .confirm-time-zone').click(function() {
            if($('#time-zone .search-input').val().length <= 3) {
                alert(chooseTimeZone);
            } else if($('#time-zone .search-input').val().length > 3) {
                $('[data-bs-target="#time-range-change-confirm"]').click()

                _userTimeZone = $('#time-zone .search-input').val()

                $('#time-zone-name-gmt').html(_userTimeZone)

                setTimeout(function () {
                    $('.not-now').click()
                }, 100)
            }
        })

        myCarousel.addEventListener('slide.bs.carousel', function () {
            previousIndex = $('.carousel').find('.active').index()
            //console.log('previous index:' + previousIndex)
        })

        myCarousel.addEventListener('slid.bs.carousel', function () {
            nextIndex = $('.carousel').find('.active').index()
            //console.log('next index:' + nextIndex)

            if (nextIndex !== 0) {
                $('.back').removeClass('display-none')
            } else {
                $('.back').addClass('display-none')
            }

            $(".progress-bar").animate({
                width: (progressPortion * nextIndex) + '%'
            }, 50)
        })


        $('.carousel').carousel({
            interval: false
        })

        $('.form-check-input').click(function () {
            if ($(this).prop('id') === 'flexRadioDefault15') {
                $('#flexRadioDefault17').prop('checked', true)

                setTimeout(function () {
                    $('#carouselExampleFade').carousel(4)
                }, 150)
            } else {
                setTimeout(function () {
                    $('#carouselExampleFade').carousel('next')
                }, 150)
            }
        })

        $('.back').click(function () {
            $('#carouselExampleFade').carousel('prev')
        })

        $('#timezone').click(function () {
            $('#time-zone').addClass('is-visible')
        })

        $('#finish').click(function () {
            batch['source'] = $('input[name="flexRadioDefaultSource"]:checked').val()
            batch['aim'] = $('input[name="flexRadioDefaultAim"]:checked').val()
            batch['level'] = $('input[name="flexRadioDefaultLevels"]:checked').val()
            batch['timezone'] = _userTimeZone

            $.ajax({
                url : _boarding,
                type : 'POST',
                async: false,
                data : {
                    '_csrf-frontend': _csrf_frontend,
                    'batch': batch
                },
                beforeSend: function() {
                    $('#finish .spinner-border').removeClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)

                    if (!data.success) {
                        alert(data.error)
                    } else {
                        let redirectUrl = '<?= Url::to(['dashboard/my-classes'], true); ?>'

                        window.location = redirectUrl
                        window.location.replace(redirectUrl)
                        window.location.href = redirectUrl

                        let testTimerID = window.setTimeout(function() {
                            window.location.href = redirectUrl
                        }, 3*250)
                    }
                },
                error : function(request, error) {
                    console.log('error')
                },
                complete: function() {
                    $('#finish .spinner-border').addClass('display-none')
                }
            });
        })
    })
</script>


<div class="container w-100" style="margin-top: 25px;max-width: 920px">
    <div class="display-flex">
        <button type="button" class="btn back display-none">
            <img src="/img/boarding/back.svg" alt="" style="flex-direction: column">
        </button>
        <div class="progress w-100">
            <div class="progress-bar" role="progressbar" style="" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>


    <div class="questions display-flex" style="align-items: center;flex-direction: column;">
        <div id="carouselExampleFade" class="carousel slide carousel-fade w-100" style="max-width: 554px;" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

            <!-- Steps Start -->
            <div class="carousel-inner display-flex" style="padding: 12px;">

                <div class="source carousel-item active" style="max-width: 530px;">
                    <h2><?= Yii::$app->devSet->getTranslate('howDidYouHearAboutUs') ?></h2>
                    <div style="margin-top: 25px;"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultSource" value="friends-family" id="flexRadioDefault1">
                        <label class="form-check-label" for="flexRadioDefault1">
                            <img src="/img/boarding/friends-family.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('throughFriendsFamily') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultSource" value="google-search" id="flexRadioDefault2">
                        <label class="form-check-label" for="flexRadioDefault2">
                            <img src="/img/boarding/google-search.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('googleSearch') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultSource" value="instagram" id="flexRadioDefault3">
                        <label class="form-check-label" for="flexRadioDefault3">
                            <img src="/img/boarding/instagram.svg" alt=""> &nbsp;
                            Instagram / Facebook
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultSource" value="youtube" id="flexRadioDefault4">
                        <label class="form-check-label" for="flexRadioDefault4">
                            <img src="/img/boarding/youtube.svg" alt=""> &nbsp;
                            YouTube
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultSource" value="tiktok" id="flexRadioDefault5">
                        <label class="form-check-label" for="flexRadioDefault5">
                            <img src="/img/boarding/tiktok.svg" alt=""> &nbsp;
                            TikTok
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultSource" value="billboard" id="flexRadioDefault6">
                        <label class="form-check-label" for="flexRadioDefault6">
                            <img src="/img/boarding/billboard.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('advertisingBoard') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultSource" value="news" id="flexRadioDefault7">
                        <label class="form-check-label" for="flexRadioDefault7">
                            <img src="/img/boarding/news.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('newsArticleBlog') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultSource" value="other" id="flexRadioDefault8">
                        <label class="form-check-label" for="flexRadioDefault8">
                            <img src="/img/boarding/other.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('other') ?>
                        </label>
                    </div>
                </div>

                <div class="aim carousel-item" style="max-width: 530px;">
                    <h2><?= Yii::$app->devSet->getTranslate('whyAreYouLearningEnglish') ?></h2>
                    <div style="margin-top: 25px;"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultAim" value="foreign-resources" id="flexRadioDefault9">
                        <label class="form-check-label" for="flexRadioDefault9">
                            <img src="/img/boarding/foreign-resources.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('useOfForeignResources') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultAim" value="new-job-opportunities" id="flexRadioDefault10">
                        <label class="form-check-label" for="flexRadioDefault10">
                            <img src="/img/boarding/permanent-job.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('newJobOpportunities') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultAim" value="studying-abroad" id="flexRadioDefault11">
                        <label class="form-check-label" for="flexRadioDefault11">
                            <img src="/img/boarding/graduate.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('studyingAbroad') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultAim" value="moving-another-country" id="flexRadioDefault12">
                        <label class="form-check-label" for="flexRadioDefault12">
                            <img src="/img/boarding/globe.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('movingToAnotherCountry') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultAim" value="travel" id="flexRadioDefault13">
                        <label class="form-check-label" for="flexRadioDefault13">
                            <img src="/img/boarding/traveler.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('travel') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultAim" value="other" id="flexRadioDefault14">
                        <label class="form-check-label" for="flexRadioDefault14">
                            <img src="/img/boarding/other.svg" alt=""> &nbsp;
                            <?= Yii::$app->devSet->getTranslate('other') ?>
                        </label>
                    </div>
                </div>

                <!--<div class="do-you-now-english carousel-item" style="max-width: 530px;">
                    <h2><?/*= Yii::$app->devSet->getTranslate('doYouKnowEnglish') */?></h2>
                    <div style="margin-top: 25px;"></div>

                    <div class="display-flex" style="justify-content: space-between;">
                        <div class="form-check" align="center">
                            <input class="form-check-input" type="radio" name="flexRadioDefaultEnglish" id="flexRadioDefault15">
                            <label class="form-check-label" for="flexRadioDefault15">
                                <img src="/img/boarding/tree-planting.svg" alt=""> &nbsp;
                                <h3><?/*= Yii::$app->devSet->getTranslate('iDoNotKnow') */?></h3>
                                <p><?/*= Yii::$app->devSet->getTranslate('iWantStartFromScratch') */?></p>
                            </label>
                        </div>
                        <div class="form-check" align="center">
                            <input class="form-check-input" type="radio" name="flexRadioDefaultEnglish" id="flexRadioDefault16">
                            <label class="form-check-label" for="flexRadioDefault16">
                                <img src="/img/boarding/bonsai.svg" alt=""> &nbsp;
                                <h3><?/*= Yii::$app->devSet->getTranslate('yesIKnow') */?></h3>
                                <p><?/*= Yii::$app->devSet->getTranslate('youCanChooseLevel') */?></p>
                            </label>
                        </div>
                    </div>
                </div>-->

                <div class="english-levels carousel-item" style="max-width: 714px;">
                    <h2><?= Yii::$app->devSet->getTranslate('howWellDoYouKnowEnglish') ?></h2>
                    <div style="margin-top: 25px;"></div>

                    <div class="display-flex" style="flex-direction: column;">
                        <!--<div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDefaultLevels" value="beginner" id="flexRadioDefault17">
                            <label class="form-check-label" for="flexRadioDefault17">
                                <div class="display-flex">
                                    <img src="/img/boarding/beginner.svg" alt="">
                                    <div class="level-description">
                                        <h3>Beginner - A0/A1</h3>
                                        <p><?/*= Yii::$app->devSet->getTranslate('iDoNotKnow') */?></p>
                                    </div>
                                </div>
                            </label>
                        </div>-->
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDefaultLevels" value="elementary" id="flexRadioDefault18">
                            <label class="form-check-label" for="flexRadioDefault18">
                                <div class="display-flex">
                                    <img src="/img/boarding/elementary.svg" alt="">
                                    <div class="level-description">
                                        <h3>Elementary - A2</h3>
                                        <p><?= Yii::$app->devSet->getTranslate('understandALittle') ?></p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDefaultLevels" value="pre-intermediate" id="flexRadioDefault19">
                            <label class="form-check-label" for="flexRadioDefault19">
                                <div class="display-flex">
                                    <img src="/img/boarding/pre-intermediate.svg" alt="">
                                    <div class="level-description">
                                        <h3>Pre-Intermediate - B1.1</h3>
                                        <p><?= Yii::$app->devSet->getTranslate('understandEveryDayTopics') ?></p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDefaultLevels" value="intermediate" id="flexRadioDefault20">
                            <label class="form-check-label" for="flexRadioDefault20">
                                <div class="display-flex">
                                    <img src="/img/boarding/intermediate.svg" alt="">
                                    <div class="level-description">
                                        <h3>Intermediate - B1.2</h3>
                                        <p><?= Yii::$app->devSet->getTranslate('canTalkAboutDifferentTopics') ?></p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDefaultLevels" value="upper-intermediate" id="flexRadioDefault21">
                            <label class="form-check-label" for="flexRadioDefault21">
                                <div class="display-flex">
                                    <img src="/img/boarding/upper-intermediate.svg" alt="">
                                    <div class="level-description">
                                        <h3>Upper-Intermediate - B2</h3>
                                        <p><?= Yii::$app->devSet->getTranslate('fullyUnderstandComplexTopics') ?></p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="time-zone carousel-item" style="max-width: 530px;" align="center">
                    <img src="/img/boarding/location.svg" alt="">
                    <br><br>

                    <h2><?= Yii::$app->devSet->getTranslate('currentTimeZone') ?></h2>

                    <div style="margin-top: 25px;"></div>

                    <div class="form-check" align="center">
                        <input class="form-check-input" type="radio" name="flexRadioDefaultEnglish" id="flexRadioDefault15">
                        <label class="form-check-label w-100" id="timezone" for="flexRadioDefault15" style="cursor: pointer;">
                            <div class="display-flex" style="justify-content: space-between;">
                                <div>
                                    <img src="/img/boarding/clock.svg" alt=""> &nbsp;
                                    <span id="time-zone-name-gmt" style="color: #4B4B4B;font-size: 16px;font-weight: 500;">
                                        ...
                                    </span>
                                </div>
                                &nbsp; &nbsp;
                                <span style="font-weight: 500;font-size: 14px;color: #1877F2;cursor: pointer;margin-top: 4px;">
                                    <?= Yii::$app->devSet->getTranslate('change') ?>
                                </span>
                            </div>
                        </label>

                        <div style="margin-top: 25px;"></div>

                        <button id="finish" type="button" class="btn w-100">
                            <?= Yii::$app->devSet->getTranslate('finish') ?>
                            <div class="spinner-border text-light display-none" role="status" style="width: 21px;height: 21px;margin-left: 5px;"></div>
                        </button>
                    </div>
                </div>

            </div>
            <!-- Steps End -->

        </div>
    </div>
</div>






<link href="<?=Yii::getAlias('@web');?>/css/dashboard/pop-up.css" rel="stylesheet">
<script src="<?=Yii::getAlias('@web');?>/js/dashboard/pop-up.js"></script>
<script src="<?=Yii::getAlias('@web');?>/js/dashboard/general.js"></script>

<div id="time-zone" class="cd-popup" role="alert">
    <div class="cd-popup-container border-radius-10" style="max-width: 390px;">

        <!-- Your Container -->
        <div class="container my-time-zone">
            <div class="row justify-content-around">
                <div class="col-11" align="center">
                    <h2><?= Yii::$app->devSet->getTranslate('chooseTimeZone') ?></h2>
                    <h4><?= Yii::$app->devSet->getTranslate('inOrderToJoin') ?></h4>
                    <br>
                </div>

                <div class="col-12" align="center" style="margin-bottom: 15px;">
                    <div class="time-zone-selector">
                        <input class="form-control mr-sm-2 search-input" type="search" placeholder="Search" aria-label="Search">

                        <div class="list-time-zone">
                            <ul class="list-group">
                                <?php $timeZones = Yii::$app->devSet->timeZones(); ?>
                                <?php foreach (DateTimeZone::listIdentifiers() as $key => $value) { ?>
                                    <li class="time-zone-content" data-time-zone="<?= $value ?>" >
                                        <?php if (isset($timeZones[$value])) { ?>
                                            <?= $timeZones[$value] ?>
                                        <?php } else { ?>
                                            <?= $value ?>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-6" align="center">
                    <h5 style="font-weight: 900;color: #2682FF;"><?= Yii::$app->devSet->getTranslate('currentTimeZone') ?></h5>
                    <h5><?= $userTimeZone ?></h5>
                    <br>
                </div>

                <div class="col-12 my-expire">
                    <div class="row justify-content-around">
                        <div class="col-5" align="left">
                            <button type="button" class="btn pretty-button position-relative not-now">
                                <?= Yii::$app->devSet->getTranslate('notNow') ?>
                            </button>
                        </div>
                        <div class="col-6" align="right">
                            <button type="button" class="btn pretty-button position-relative confirm-time-zone">
                                <span class="spinner-grow spinner-grow-sm display-none" role="status" aria-hidden="true"></span>
                                <?= Yii::$app->devSet->getTranslate('save') ?>
                            </button>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>

        <div class="cd-popup-close img-replace"><?= Yii::$app->devSet->getTranslate('close') ?></div>
    </div>
</div>