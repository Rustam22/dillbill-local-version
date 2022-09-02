<?php

use common\models\UserParameters;
use common\models\UserProfile;
use frontend\assets\BasicAppAsset;
use yii\helpers\Url;

BasicAppAsset::register($this);

if(Yii::$app->user->identity->userProfile->color == null) {
    $colors = [
            1 => '#FBBC04',
            2 => '#35C9D4',
            3 => '#DD33AC',
            4 => '#00B67A',
            5 => '#EE6011',
            6 => '#1877F2',
            7 => '#763BE0',
            8 => '#2B82ED'
    ];
    $randColor = shuffle($colors);
    $color = $colors[$randColor];

    $userProfile = UserProfile::findOne(['userId' => Yii::$app->user->id]);
    $userProfile->color = $color;

    $userProfile->save(false);
}

$userLevel = Yii::$app->user->identity->userParameters->currentLevel;

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

$userTimeZone = (Yii::$app->user->identity->userProfile->timezone == null) ? Yii::$app->timeZone : Yii::$app->user->identity->userProfile->timezone;

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <title>Dashboard | DillBill</title>

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-WWXHM4Z');</script>
        <!-- End Google Tag Manager -->

        <script>
            !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on","addSourceMiddleware","addIntegrationMiddleware","setAnonymousId","addDestinationMiddleware"];analytics.factory=function(e){return function(){var t=Array.prototype.slice.call(arguments);t.unshift(e);analytics.push(t);return analytics}};for(var e=0;e<analytics.methods.length;e++){var key=analytics.methods[e];analytics[key]=analytics.factory(key)}analytics.load=function(key,e){var t=document.createElement("script");t.type="text/javascript";t.async=!0;t.src="https://cdn.segment.com/analytics.js/v1/" + key + "/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);analytics._loadOptions=e};analytics._writeKey="YVOTkDLn3lkkrVYKFo7KToYh5b44XO41";;analytics.SNIPPET_VERSION="4.15.3";
                analytics.load("YVOTkDLn3lkkrVYKFo7KToYh5b44XO41");
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

        <style>@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap');</style>
    </head>


    <body>

        <!--------------- Manuals --------------->
        <link href="<?=Yii::getAlias('@web');?>/css/dashboard/general.css" rel="stylesheet">

        <script>
            let _csrf_frontend = '<?= Yii::$app->request->csrfToken; ?>'

            let UserCurrentTimeZone = '<?= Yii::$app->user->identity->userProfile->timezone ?>'
            let _timeZoneAssigning = "<?= Url::to(['dashboard/time-zone-assign'], true); ?>"
            let chooseTimeZone = "<?= Yii::$app->devSet->getTranslate('chooseTimeZone') ?>"

            let _googleCalendar = "<?= Url::to(['dashboard/google-calendar'], true); ?>"
            let gmailConnectedToGoogleCalendar = "<?= Yii::$app->devSet->getTranslate('gmailConnectedToGoogleCalendar') ?>"
            let gmailDisconnectedToGoogleCalendar = "<?= Yii::$app->devSet->getTranslate('gmailDisconnectedFromGoogleCalendar') ?>"

            let _levelTest = "<?= Url::to(['dashboard/level-test'], true); ?>"
            let _timeAvailability = "<?= Url::to(['dashboard/time-availability'], true); ?>"
        </script>

        <script src="<?=Yii::getAlias('@web');?>/js/dashboard/general.js"></script>



        <link href="<?=Yii::getAlias('@web');?>/css/dashboard/side-bar.css" rel="stylesheet">
        <script src="<?=Yii::getAlias('@web');?>/js/dashboard/side-bar.js"></script>


        <?php if (Yii::$app->controller->id != 'payment') { ?>
        <div class="side-bar position-fixed h-100 w-100">
            <a href="<?= Url::to(['dashboard/my-classes']) ?>">
                <img id="side-logo" src="<?= Yii::getAlias('@web') ?>/img/dashboard/web-dash-logo.svg" alt="">
            </a>

            <div class="side-menu">
                <a href="<?= Url::to(['dashboard/my-classes']) ?>">
                    <div class="inside-menu cursor-pointer <?php if(Yii::$app->controller->action->id == 'my-classes') { ?> active-menu <?php } ?>">
                        <div class="circle-icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.58424 1.48068C7.82711 1.28639 8.17221 1.28639 8.41508 1.48068L15.0817 6.81402C15.3685 7.04345 15.415 7.46193 15.1856 7.74872C14.9562 8.03551 14.5377 8.082 14.2509 7.85257L13.6647 7.38358V14C13.6647 14.3672 13.3669 14.665 12.9997 14.665H2.99966C2.63239 14.665 2.33466 14.3672 2.33466 14V7.38358L1.74842 7.85257C1.46163 8.082 1.04315 8.03551 0.813716 7.74872C0.584284 7.46193 0.630782 7.04345 0.917571 6.81402L7.58424 1.48068ZM3.66466 6.31958L7.99966 2.85158L12.3347 6.31958V13.335H3.66466V6.31958Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.66797 9.66671C5.66797 9.29944 5.9657 9.00171 6.33297 9.00171H9.6663C10.0336 9.00171 10.3313 9.29944 10.3313 9.66671V14C10.3313 14.3673 10.0336 14.665 9.6663 14.665H6.33297C5.9657 14.665 5.66797 14.3673 5.66797 14V9.66671ZM6.99797 10.3317V13.335H9.0013V10.3317H6.99797Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.33496 14C2.33496 13.6327 2.63269 13.335 2.99996 13.335H13C13.3672 13.335 13.665 13.6327 13.665 14C13.665 14.3672 13.3672 14.665 13 14.665H2.99996C2.63269 14.665 2.33496 14.3672 2.33496 14Z" fill="#333333"/>
                            </svg>
                        </div>
                        <span><?= Yii::$app->devSet->getTranslate('myClasses') ?></span>
                    </div>
                </a>

                <a href="<?= Url::to(['dashboard/grammar']) ?>">
                    <div class="inside-menu cursor-pointer <?php if(Yii::$app->controller->action->id == 'grammar') { ?> active-menu <?php } ?>">
                        <div class="circle-icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.00195 2.33321C1.00195 1.96594 1.29968 1.66821 1.66695 1.66821H5.33362C7.17366 1.66821 8.66529 3.15984 8.66529 4.99988V13.9999C8.66529 14.3671 8.36756 14.6649 8.00029 14.6649C7.63302 14.6649 7.33529 14.3671 7.33529 13.9999C7.33529 13.2626 6.73758 12.6649 6.00029 12.6649H1.66695C1.29968 12.6649 1.00195 12.3671 1.00195 11.9999V2.33321ZM7.33529 11.6929V4.99988C7.33529 3.89438 6.43912 2.99821 5.33362 2.99821H2.33195V11.3349H6.00029C6.48673 11.3349 6.94274 11.4652 7.33529 11.6929Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.33496 4.99988C7.33496 3.15984 8.82659 1.66821 10.6666 1.66821H14.3333C14.7006 1.66821 14.9983 1.96594 14.9983 2.33321V11.9999C14.9983 12.3671 14.7006 12.6649 14.3333 12.6649H9.99996C9.26266 12.6649 8.66496 13.2626 8.66496 13.9999C8.66496 14.3671 8.36723 14.6649 7.99996 14.6649C7.63269 14.6649 7.33496 14.3671 7.33496 13.9999V4.99988ZM8.66496 11.6929C9.05751 11.4652 9.51352 11.3349 9.99996 11.3349H13.6683V2.99821H10.6666C9.56113 2.99821 8.66496 3.89438 8.66496 4.99988V11.6929Z" fill="#333333"/>
                            </svg>
                        </div>
                        <span><?= Yii::$app->devSet->getTranslate('grammar') ?></span>
                    </div>
                </a>

                <!--<a href="<?/*= Url::to(['dashboard/certificate']) */?>">
                    <div class="inside-menu cursor-pointer <?php /*if(Yii::$app->controller->action->id == 'certificate') { */?> active-menu <?php /*} */?>">
                        <div class="circle-icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.7134 1.09248C9.81272 0.836718 10.0589 0.668213 10.3333 0.668213H14.6666C14.8846 0.668213 15.0888 0.775054 15.213 0.954167C15.3373 1.13328 15.3659 1.36192 15.2895 1.56611L13.0461 7.56611C12.9544 7.8114 12.7273 7.98003 12.4659 7.99684C12.2046 8.01365 11.9577 7.87551 11.8353 7.64399C11.2377 6.5133 10.1551 5.67963 8.86767 5.41829C8.67553 5.37929 8.51039 5.25748 8.41639 5.08543C8.32238 4.91338 8.30909 4.70861 8.38006 4.52585L9.7134 1.09248ZM10.7884 1.99821L9.88557 4.32311C10.8036 4.64703 11.6125 5.20042 12.2439 5.91403L13.708 1.99821H10.7884Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.786576 0.954167C0.91083 0.775054 1.11498 0.668213 1.33297 0.668213H5.66631C5.94068 0.668213 6.18688 0.836718 6.2862 1.09248L7.61954 4.52585C7.69051 4.70861 7.67722 4.91338 7.58321 5.08543C7.48921 5.25748 7.32407 5.37929 7.13193 5.41829C5.84446 5.67963 4.76194 6.5133 4.16425 7.64399C4.04187 7.87551 3.795 8.01365 3.53365 7.99684C3.27231 7.98003 3.04517 7.8114 2.95345 7.56611L0.710088 1.56611C0.633743 1.36192 0.662322 1.13328 0.786576 0.954167ZM2.29157 1.99821L3.75568 5.91403C4.38709 5.20042 5.196 4.64703 6.11403 4.32311L5.21117 1.99821H2.29157Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.99996 5.33171C7.70218 5.33171 7.41207 5.36164 7.13228 5.41845L7.13225 5.41845C5.84479 5.67979 4.76226 6.51346 4.16458 7.64415L4.16457 7.64415C3.84576 8.24727 3.66496 8.93489 3.66496 9.66671C3.66496 12.0609 5.6058 14.0017 7.99996 14.0017C10.3941 14.0017 12.335 12.0609 12.335 9.66671C12.335 8.93489 12.1542 8.24727 11.8353 7.64415C11.2377 6.51346 10.1551 5.67979 8.86767 5.41845L8.86764 5.41845C8.58786 5.36164 8.29775 5.33171 7.99996 5.33171ZM6.86764 4.11504C7.23406 4.04065 7.61281 4.00171 7.99996 4.00171C8.38711 4.00171 8.76587 4.04065 9.13228 4.11504C10.8191 4.45744 12.2315 5.54756 13.0112 7.0226L12.4233 7.33338L13.0112 7.02261C13.4288 7.81262 13.665 8.71306 13.665 9.66671C13.665 12.7954 11.1287 15.3317 7.99996 15.3317C4.87126 15.3317 2.33496 12.7954 2.33496 9.66671C2.33496 8.71306 2.57113 7.81262 2.98875 7.0226C3.76846 5.54756 5.18084 4.45744 6.86764 4.11504ZM6.99945 4.76425L6.86764 4.11504L6.99945 4.76425Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.38912 6.79374C8.56253 6.91873 8.66529 7.11946 8.66529 7.33322V11.0016H9.33362C9.70089 11.0016 9.99862 11.2993 9.99862 11.6666C9.99862 12.0338 9.70089 12.3316 9.33362 12.3316H8.00769C8.00237 12.3316 7.99706 12.3316 7.99176 12.3316H6.66695C6.29968 12.3316 6.00195 12.0338 6.00195 11.6666C6.00195 11.2993 6.29968 11.0016 6.66695 11.0016H7.14857L6.35514 7.82784C6.27086 7.49073 6.46034 7.14556 6.78999 7.03568L7.78999 6.70234C7.99279 6.63475 8.21571 6.66875 8.38912 6.79374Z" fill="#333333"/>
                            </svg>
                        </div>
                        <span><?/*= Yii::$app->devSet->getTranslate('certificate') */?></span>
                    </div>
                </a>-->

                <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') { ?>
                    <!--<div class="inside-menu cursor-pointer" id="level-test">
                        <div class="circle-icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.60766 0.795961C7.84117 0.62563 8.15796 0.62563 8.39146 0.795961L9.96687 1.94515L11.9169 1.94143C12.2059 1.94088 12.4622 2.12709 12.551 2.40214L13.15 4.25787L14.7298 5.40104C14.9639 5.57048 15.0618 5.87177 14.972 6.14648L14.3659 7.99988L14.972 9.85328C15.0618 10.128 14.9639 10.4293 14.7298 10.5987L13.15 11.7419L12.551 13.5976C12.4622 13.8727 12.2059 14.0589 11.9169 14.0583L9.96687 14.0546L8.39146 15.2038C8.15796 15.3741 7.84117 15.3741 7.60767 15.2038L6.03226 14.0546L4.08226 14.0583C3.79323 14.0589 3.53695 13.8727 3.44815 13.5976L2.8491 11.7419L1.26933 10.5987C1.03518 10.4293 0.937288 10.128 1.02713 9.85328L1.63324 7.99988L1.02713 6.14648C0.937288 5.87177 1.03518 5.57048 1.26933 5.40104L2.8491 4.25787L3.44815 2.40214C3.53695 2.12709 3.79323 1.94088 4.08226 1.94143L6.03226 1.94515L7.60766 0.795961ZM7.99956 2.15634L6.64036 3.14781C6.52621 3.23108 6.3885 3.27583 6.2472 3.27556L4.56482 3.27236L4.04797 4.8734C4.00457 5.00787 3.91945 5.12502 3.80498 5.20785L2.44203 6.19414L2.96496 7.79318C3.00888 7.92748 3.00888 8.07228 2.96496 8.20658L2.44203 9.80562L3.80498 10.7919C3.91945 10.8747 4.00456 10.9919 4.04797 11.1264L4.56482 12.7274L6.24721 12.7242C6.3885 12.7239 6.52621 12.7687 6.64036 12.852L7.99956 13.8434L9.35877 12.852C9.47292 12.7687 9.61063 12.7239 9.75192 12.7242L11.4343 12.7274L11.9512 11.1264C11.9946 10.9919 12.0797 10.8747 12.1941 10.7919L13.5571 9.80562L13.0342 8.20658C12.9903 8.07228 12.9903 7.92748 13.0342 7.79318L13.5571 6.19414L12.1941 5.20785C12.0797 5.12502 11.9946 5.00787 11.9512 4.8734L11.4343 3.27236L9.75193 3.27556C9.61063 3.27583 9.47292 3.23108 9.35876 3.14781L7.99956 2.15634Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1372 5.86299C11.3969 6.12269 11.3969 6.54374 11.1372 6.80344L7.80385 10.1368C7.54415 10.3965 7.12309 10.3965 6.86339 10.1368L5.19673 8.47011C4.93703 8.21041 4.93703 7.78935 5.19673 7.52965C5.45643 7.26995 5.87748 7.26995 6.13718 7.52965L7.33362 8.72609L10.1967 5.86299C10.4564 5.60329 10.8775 5.60329 11.1372 5.86299Z" fill="#333333"/>
                            </svg>
                        </div>
                        <span><?/*= Yii::$app->devSet->getTranslate('levelTestTitle') */?></span>
                        <div class="spinner-grow text-primary display-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>-->
                <?php } ?>

                <a href="<?= Url::to(['dashboard/class-history']) ?>">
                    <div class="inside-menu cursor-pointer position-relative <?php if(Yii::$app->controller->action->id == 'class-history') { ?> active-menu <?php } ?>">
                        <div class="circle-icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.93941 1.57739C2.30668 1.57739 2.60441 1.87512 2.60441 2.24239V4.00163H4.36367C4.73094 4.00163 5.02867 4.29936 5.02867 4.66663C5.02867 5.0339 4.73094 5.33163 4.36367 5.33163H1.93941C1.57214 5.33163 1.27441 5.0339 1.27441 4.66663V2.24239C1.27441 1.87512 1.57214 1.57739 1.93941 1.57739Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.64963 4.33287C2.9164 2.14354 5.28535 0.668213 7.99964 0.668213C12.0488 0.668213 15.3313 3.95071 15.3313 7.99988C15.3313 12.049 12.0488 15.3315 7.99964 15.3315C3.95047 15.3315 0.667969 12.049 0.667969 7.99988C0.667969 7.63261 0.965699 7.33488 1.33297 7.33488C1.70024 7.33488 1.99797 7.63261 1.99797 7.99988C1.99797 11.3145 4.685 14.0015 7.99964 14.0015C11.3143 14.0015 14.0013 11.3145 14.0013 7.99988C14.0013 4.68525 11.3143 1.99821 7.99964 1.99821C5.77925 1.99821 3.83961 3.20363 2.80082 4.99896C2.61688 5.31685 2.21007 5.42544 1.89218 5.24151C1.57429 5.05757 1.4657 4.65076 1.64963 4.33287Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.0014 3.33496C8.36867 3.335 8.66637 3.63276 8.66634 4.00003L8.66597 7.72747L11.2976 10.3591C11.5573 10.6188 11.5573 11.0399 11.2976 11.2996C11.0379 11.5593 10.6169 11.5593 10.3572 11.2996L7.53071 8.47312C7.40598 8.34839 7.33592 8.17922 7.33594 8.00283L7.33634 3.99989C7.33637 3.63263 7.63413 3.33492 8.0014 3.33496Z" fill="#333333"/>
                            </svg>
                        </div>
                        <span><?= Yii::$app->devSet->getTranslate('classHistory') ?></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="badge" style="color: white;font-size: 10px;font-weight: 600;background: #FF7F00;padding: 3px 5px 4px;">
                            new
                        </span>
                    </div>
                </a>

                <div style="border-top: 1px solid #E0E2E7; margin: 10px auto 27px; width: 93%;"></div>

                <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') { ?>
                    <?php
                        $diff = 8;
                        $alignedUserTime = '';

                        if(Yii::$app->user->identity->userParameters->availabilityLCD != null) {
                            $lastDate = date_create(Yii::$app->user->identity->userParameters->availabilityLCD);
                            $currentDate = date_create(date('Y-m-d'));
                            $diff = date_diff($currentDate, $lastDate)->format('%a');

                            $alignedUserTime = Yii::$app->devSet->getDateByTimeZone($userTimeZone);
                            $alignedUserTime = date('d.m.Y', strtotime($alignedUserTime->format('Y-m-d'). ' + '.(7 - $diff).' days'));
                        }
                    ?>
                    <div class="inside-menu cursor-pointer tooltip-my" <?php if($diff >= 7) { ?> data-popup-id="time-availability" <?php } ?> >
                        <div class="circle-icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.667969 7.99988C0.667969 3.95071 3.95047 0.668213 7.99964 0.668213C12.0488 0.668213 15.3313 3.95071 15.3313 7.99988C15.3313 12.049 12.0488 15.3315 7.99964 15.3315C3.95047 15.3315 0.667969 12.049 0.667969 7.99988ZM7.99964 1.99821C4.685 1.99821 1.99797 4.68525 1.99797 7.99988C1.99797 11.3145 4.685 14.0015 7.99964 14.0015C11.3143 14.0015 14.0013 11.3145 14.0013 7.99988C14.0013 4.68525 11.3143 1.99821 7.99964 1.99821Z" fill="#333333"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00238 3.33496C8.36965 3.335 8.66735 3.63276 8.66731 4.00003L8.66694 7.72747L11.2986 10.3591C11.5583 10.6188 11.5583 11.0399 11.2986 11.2996C11.0389 11.5593 10.6179 11.5593 10.3582 11.2996L7.53169 8.47312C7.40696 8.34839 7.3369 8.17922 7.33691 8.00283L7.33731 3.99989C7.33735 3.63263 7.63511 3.33492 8.00238 3.33496Z" fill="#333333"/>
                            </svg>
                        </div>
                        <span><?= Yii::$app->devSet->getTranslate('mySchedule') ?></span>

                        <?php if(Yii::$app->user->identity->userParameters->availability == null) { ?>
                            <div class="red-dot"></div>
                        <?php } ?>

                        <?php if($diff < 7) { ?>
                            <svg style="margin-left: 10px;margin-top: 1px;" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.3335 14.6667H12.6668C13.1973 14.6667 13.706 14.456 14.081 14.0809C14.4561 13.7058 14.6668 13.1971 14.6668 12.6667V6.66667C14.6668 6.13624 14.4561 5.62753 14.081 5.25245C13.706 4.87738 13.1973 4.66667 12.6668 4.66667H11.3335V4C11.3335 3.11595 10.9823 2.2681 10.3572 1.64298C9.73206 1.01786 8.88422 0.666668 8.00016 0.666668C7.11611 0.666668 6.26826 1.01786 5.64314 1.64298C5.01802 2.2681 4.66683 3.11595 4.66683 4V4.66667H3.3335C2.80306 4.66667 2.29436 4.87738 1.91928 5.25245C1.54421 5.62753 1.3335 6.13624 1.3335 6.66667V12.6667C1.3335 13.1971 1.54421 13.7058 1.91928 14.0809C2.29436 14.456 2.80306 14.6667 3.3335 14.6667ZM6.00016 4C6.00016 3.46957 6.21088 2.96086 6.58595 2.58579C6.96102 2.21072 7.46973 2 8.00016 2C8.5306 2 9.0393 2.21072 9.41438 2.58579C9.78945 2.96086 10.0002 3.46957 10.0002 4V4.66667H6.00016V4ZM2.66683 6.66667C2.66683 6.48986 2.73707 6.32029 2.86209 6.19526C2.98712 6.07024 3.15669 6 3.3335 6H12.6668C12.8436 6 13.0132 6.07024 13.1382 6.19526C13.2633 6.32029 13.3335 6.48986 13.3335 6.66667V12.6667C13.3335 12.8435 13.2633 13.013 13.1382 13.1381C13.0132 13.2631 12.8436 13.3333 12.6668 13.3333H3.3335C3.15669 13.3333 2.98712 13.2631 2.86209 13.1381C2.73707 13.013 2.66683 12.8435 2.66683 12.6667V6.66667ZM6.66683 9.66667C6.66683 9.40296 6.74503 9.14517 6.89154 8.92591C7.03805 8.70664 7.24628 8.53575 7.48992 8.43483C7.73355 8.33391 8.00164 8.30751 8.26028 8.35895C8.51892 8.4104 8.7565 8.53739 8.94297 8.72386C9.12944 8.91033 9.25643 9.14791 9.30788 9.40655C9.35932 9.66519 9.33292 9.93328 9.232 10.1769C9.13109 10.4205 8.96019 10.6288 8.74092 10.7753C8.52166 10.9218 8.26387 11 8.00016 11C7.64654 11 7.3074 10.8595 7.05735 10.6095C6.80731 10.3594 6.66683 10.0203 6.66683 9.66667Z" fill="#202734"/>
                            </svg>

                            <div class="top">
                                <span style="color: #333333;font-size: 14px;font-weight: 400;">
                                    <?= Yii::$app->devSet->getTranslate('nextDateToChangeSchedule') ?>
                                    <span style="color: #1877F2;font-weight: 600;">
                                        <?= $alignedUserTime ?>
                                    </span>
                                </span>
                                <i></i>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div class="inside-menu cursor-pointer" data-popup-id="google-calendar">
                    <div class="circle-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.99964 1.99821C5.78957 1.99821 3.99797 3.78982 3.99797 5.99988V12.0015H12.0013V5.99988C12.0013 3.78982 10.2097 1.99821 7.99964 1.99821ZM13.3313 12.0015V5.99988C13.3313 3.05528 10.9442 0.668213 7.99964 0.668213C5.05503 0.668213 2.66797 3.05528 2.66797 5.99988V12.0015H1.33297C0.965699 12.0015 0.667969 12.2993 0.667969 12.6665C0.667969 13.0338 0.965699 13.3315 1.33297 13.3315H14.6663C15.0336 13.3315 15.3313 13.0338 15.3313 12.6665C15.3313 12.2993 15.0336 12.0015 14.6663 12.0015H13.3313Z" fill="#333333"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.66797 12.6667C5.66797 12.2994 5.9657 12.0017 6.33297 12.0017H9.6663C10.0336 12.0017 10.3313 12.2994 10.3313 12.6667V13C10.3313 14.2878 9.28737 15.3317 7.99964 15.3317C6.7119 15.3317 5.66797 14.2878 5.66797 13V12.6667ZM7.05418 13.3317C7.19107 13.7219 7.56268 14.0017 7.99964 14.0017C8.43659 14.0017 8.80821 13.7219 8.94509 13.3317H7.05418Z" fill="#333333"/>
                        </svg>
                    </div>
                    <span><?= Yii::$app->devSet->getTranslate('notifications') ?></span>
                    <?php if((Yii::$app->user->identity->userParameters->googleCalendar == 'no') AND (Yii::$app->user->identity->userParameters->cp > 0) AND (Yii::$app->user->identity->userParameters->currentLevel != 'empty')) { ?>
                        <div class="red-dot"></div>
                    <?php } ?>
                </div>

                <!--<div class="invite-friend inside-menu cursor-pointer">
                    <div class="circle-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.66797 6.66671C1.66797 6.29944 1.9657 6.00171 2.33297 6.00171H13.6663C14.0336 6.00171 14.3313 6.29944 14.3313 6.66671V14.6667C14.3313 15.034 14.0336 15.3317 13.6663 15.3317H2.33297C1.9657 15.3317 1.66797 15.034 1.66797 14.6667V6.66671ZM2.99797 7.33171V14.0017H13.0013V7.33171H2.99797Z" fill="#333333"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.99996 6.00171C8.36723 6.00171 8.66496 6.29944 8.66496 6.66671V14.6667C8.66496 15.034 8.36723 15.3317 7.99996 15.3317C7.63269 15.3317 7.33496 15.034 7.33496 14.6667V6.66671C7.33496 6.29944 7.63269 6.00171 7.99996 6.00171Z" fill="#333333"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.66797 14.6667C1.66797 14.2994 1.9657 14.0017 2.33297 14.0017H13.6663C14.0336 14.0017 14.3313 14.2994 14.3313 14.6667C14.3313 15.034 14.0336 15.3317 13.6663 15.3317H2.33297C1.9657 15.3317 1.66797 15.034 1.66797 14.6667Z" fill="#333333"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.667969 3.99996C0.667969 3.63269 0.965699 3.33496 1.33297 3.33496H14.6663C15.0336 3.33496 15.3313 3.63269 15.3313 3.99996V6.66663C15.3313 7.0339 15.0336 7.33163 14.6663 7.33163H1.33297C0.965699 7.33163 0.667969 7.0339 0.667969 6.66663V3.99996ZM1.99797 4.66496V6.00163H14.0013V4.66496H1.99797Z" fill="#333333"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.86274 0.862987C5.12244 0.603288 5.5435 0.603288 5.80319 0.862987L7.99964 3.05943L10.1961 0.862987C10.4558 0.603288 10.8768 0.603288 11.1365 0.862987C11.3962 1.12269 11.3962 1.54374 11.1365 1.80344L8.46986 4.47011C8.21016 4.7298 7.78911 4.7298 7.52941 4.47011L4.86274 1.80344C4.60304 1.54374 4.60304 1.12269 4.86274 0.862987Z" fill="#333333"/>
                        </svg>
                    </div>
                    <span><?/*= Yii::$app->devSet->getTranslate('getDiscount') */?></span>
                    <?php /*if((Yii::$app->user->identity->googleCalendar == 'no') AND (Yii::$app->user->identity->cp > 0) AND (Yii::$app->user->identity->currentLevel != 'empty')) { */?>
                        <div class="red-dot"></div>
                    <?php /*} */?>
                </div>-->

                <div class="dropdown lang-dropdown w-100">
                    <button class="btn dropdown-toggle w-100" type="button" id="dropdownMenuButton112" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if(Yii::$app->language == 'en') { ?><div class="flag-frame" style="background-position: -196px -22px;"></div> English    <?php } ?>
                        <?php if(Yii::$app->language == 'az') { ?><div class="flag-frame" style="background-position: 0 -44px;"></div> Azərbaycan <?php } ?>
                        <?php if(Yii::$app->language == 'tr') { ?><div class="flag-frame" style="background-position: -125px -176px;"></div> Türkçe     <?php } ?>
                        <?php if(Yii::$app->language == 'ru') { ?><div class="flag-frame" style="background-position: -168px -88px;"></div> Русский    <?php } ?>
                        <?php if(Yii::$app->language == 'pt') { ?><div class="flag-frame" style="background-position: -70px -11px;"></div> Português (BR)    <?php } ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton112">
                        <li>
                            <a class="dropdown-item" href="<?= Yii::$app->request->hostInfo.'/en/'.Yii::$app->controller->action->id ?>">
                                <div class="flag-frame" style="background-position: -196px -22px;"></div> English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= Yii::$app->request->hostInfo.'/az/'.Yii::$app->controller->action->id ?>">
                                <div class="flag-frame" style="background-position: 0 -44px;"></div> Azərbaycan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= Yii::$app->request->hostInfo.'/tr/'.Yii::$app->controller->action->id ?>">
                                <div class="flag-frame" style="background-position: -125px -176px;"></div> Türkçe
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= Yii::$app->request->hostInfo.'/ru/'.Yii::$app->controller->action->id ?>">
                                <div class="flag-frame" style="background-position: -168px -88px;"></div> Русский
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= Yii::$app->request->hostInfo.'/pt/'.Yii::$app->controller->action->id ?>">
                                <div class="flag-frame" style="background-position: -70px -11px;"></div> Português (BR)
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
        <div class="overlay display-none"></div>
        <?php } ?>




        <link href="<?=Yii::getAlias('@web');?>/css/dashboard/nav-bar.css" rel="stylesheet">

        <nav class="navbar neo-navbar navbar-expand-lg navbar-light"
            <?php if (Yii::$app->controller->id == 'payment') { ?>
                style="margin-left: 0;"
            <?php } ?>
        >
            <div class="container-fluid">
                <a class="navbar-brand desktop-active-menu"
                    <?php if (Yii::$app->controller->id == 'payment') { ?>
                        href="<?= Url::to(['dashboard/my-classes']) ?>"
                    <?php } ?>
                >
                    <?php if (Yii::$app->controller->id == 'payment') { ?>
                        <img width="116" src="<?= Yii::getAlias('@web') ?>/img/dashboard/web-dash-logo.svg" alt="">
                    <?php } ?>
                    <?php if(Yii::$app->controller->action->id == 'my-classes') { ?>
                        <?= Yii::$app->devSet->getTranslate('myClasses') ?>
                    <?php } ?>
                    <?php if(Yii::$app->controller->action->id == 'grammar') { ?>
                        <?= Yii::$app->devSet->getTranslate('grammar') ?>
                    <?php } ?>
                    <?php if(Yii::$app->controller->action->id == 'level-test') { ?>
                        <?= Yii::$app->devSet->getTranslate('levelTestTitle') ?>
                    <?php } ?>
                    <?php if(Yii::$app->controller->action->id == 'class-history') { ?>
                        <?= Yii::$app->devSet->getTranslate('classHistory') ?>
                    <?php } ?>
                    <?php if(Yii::$app->controller->action->id == 'certificate') { ?>
                        <?= Yii::$app->devSet->getTranslate('certificate') ?>
                    <?php } ?>
                </a>

                <div class="mobile-hamburger display-none cursor-pointer">
                    <?php if (Yii::$app->controller->id != 'payment') { ?>
                    <div class="navbar-hamburger position-relative" style="flex-direction: column;margin-top: 7px;margin-right: 12px;">
                        <?php if((Yii::$app->user->identity->userParameters->googleCalendar == 'no' OR Yii::$app->user->identity->userParameters->availability == null) AND (Yii::$app->user->identity->userParameters->cp > 0) AND (Yii::$app->user->identity->userParameters->currentLevel != 'empty')) { ?>
                            <div class="red-dot position-absolute" style="top: 0; left:5px; width: 10px; height: 10px;"></div>
                        <?php } ?>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 4C2 3.73478 2.10536 3.48043 2.29289 3.29289C2.48043 3.10536 2.73478 3 3 3H21C21.2652 3 21.5196 3.10536 21.7071 3.29289C21.8946 3.48043 22 3.73478 22 4C22 4.26522 21.8946 4.51957 21.7071 4.70711C21.5196 4.89464 21.2652 5 21 5H3C2.73478 5 2.48043 4.89464 2.29289 4.70711C2.10536 4.51957 2 4.26522 2 4ZM3 13H21C21.2652 13 21.5196 12.8946 21.7071 12.7071C21.8946 12.5196 22 12.2652 22 12C22 11.7348 21.8946 11.4804 21.7071 11.2929C21.5196 11.1054 21.2652 11 21 11H3C2.73478 11 2.48043 11.1054 2.29289 11.2929C2.10536 11.4804 2 11.7348 2 12C2 12.2652 2.10536 12.5196 2.29289 12.7071C2.48043 12.8946 2.73478 13 3 13ZM3 21H21C21.2652 21 21.5196 20.8946 21.7071 20.7071C21.8946 20.5196 22 20.2652 22 20C22 19.7348 21.8946 19.4804 21.7071 19.2929C21.5196 19.1054 21.2652 19 21 19H3C2.73478 19 2.48043 19.1054 2.29289 19.2929C2.10536 19.4804 2 19.7348 2 20C2 20.2652 2.10536 20.5196 2.29289 20.7071C2.48043 20.8946 2.73478 21 3 21Z" fill="black"/>
                        </svg>
                    </div>
                    <?php } ?>
                    <div style="flex-direction: column;">
                        <a href="<?=Url::to(['dashboard/my-classes'], true) ?>">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="40" height="40" rx="8" fill="#F4F8FB"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M25.6029 10.0181C25.1258 10.0181 24.6682 10.209 24.3309 10.5488C23.9936 10.8886 23.804 11.3495 23.804 11.83V16.0665C23.5462 15.9132 23.2791 15.7762 23.0044 15.6564C22.1762 15.3011 21.2879 15.1098 20.3879 15.0931H20.0331C19.1336 15.1058 18.2452 15.2938 17.4166 15.6465C17.1412 15.7629 16.8735 15.8971 16.6153 16.0484V11.8119C16.6153 11.3313 16.4258 10.8705 16.0885 10.5307C15.7511 10.1909 15.2936 10 14.8165 10C14.3394 10 13.8819 10.1909 13.5446 10.5307C13.2072 10.8705 13.0177 11.3313 13.0177 11.8119L13.0013 22.5515C12.977 23.8405 13.2828 25.1141 13.8893 26.2494L13.9531 26.3713C13.9605 26.3846 13.9693 26.3998 13.9788 26.416C13.9985 26.4497 14.021 26.4883 14.0398 26.5261C14.7974 27.8565 15.9752 28.8932 17.3855 29.4713C17.989 29.7209 18.6233 29.8871 19.271 29.9654C19.4509 29.9868 19.6324 30.0016 19.8172 30.0115H20.1884H20.5384C20.7124 30.0115 20.8835 29.9911 21.0531 29.9708C21.0636 29.9696 21.0741 29.9683 21.0846 29.9671C21.3241 29.9394 21.5621 29.8993 21.7975 29.8468C22.7374 29.6417 23.625 29.2432 24.4048 28.6763C25.1845 28.1094 25.8396 27.3863 26.3289 26.5525C26.3352 26.5416 26.3414 26.5309 26.3476 26.5201C26.3708 26.4803 26.3937 26.4408 26.4156 26.3993C26.4434 26.3466 26.4597 26.3169 26.4826 26.2757C26.6031 26.0497 26.7123 25.8177 26.8097 25.5806C27.1952 24.6288 27.3887 23.6091 27.3788 22.5811L27.3984 11.8267C27.3975 11.3479 27.2085 10.8889 26.8727 10.55C26.5369 10.2111 26.0815 10.0199 25.6061 10.0181H25.6029ZM23.4673 24.0902C23.2891 24.5398 23.0277 24.9512 22.6971 25.3025C22.3711 25.6447 21.9824 25.9199 21.5524 26.1129C21.464 26.1525 21.3725 26.1904 21.2809 26.2217L20.2212 27.2824L19.2155 26.2595C19.0768 26.2171 18.9408 26.1659 18.8083 26.1064C18.3808 25.9082 17.9954 25.6285 17.6735 25.2828C17.3494 24.9289 17.0947 24.5164 16.9229 24.0672C16.7314 23.5844 16.6364 23.0683 16.6432 22.5485C16.6435 22.3489 16.6577 22.1497 16.6857 21.9522C16.7305 21.6697 16.8073 21.3934 16.9147 21.1286C17.0921 20.6772 17.3486 20.2616 17.6718 19.9014C17.9978 19.5479 18.3859 19.258 18.8165 19.0466C19.134 18.8898 19.4758 18.7889 19.8271 18.7484L19.9907 18.7352H20.4191L20.5826 18.7484C20.9374 18.7909 21.2821 18.8951 21.6014 19.0564C22.0295 19.2687 22.414 19.5604 22.7347 19.9163C23.0593 20.2763 23.3164 20.6926 23.4934 21.1451C23.8852 22.3046 23.6392 23.5916 23.4673 24.0902Z" fill="#1877F2"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="d-flex">
                    <?php if (Yii::$app->controller->id == 'payment') { ?>
                        <a href="<?= Url::to(['dashboard/my-classes'], true) ?>">
                            <button class="btn subscribe-button">
                                &nbsp;<?= Yii::$app->devSet->getTranslate('goToDashboard') ?>
                            </button>
                        </a>
                    <?php } else { ?>
                        <?php if (Yii::$app->user->identity->userParameters->currentLevel != 'empty' AND Yii::$app->user->identity->userParameters->cp > 0) { ?>
                                <style>
                                    .lesson-balance {
                                        border: 1px solid #CBD1D3;
                                        border-radius: 8px;
                                        color: #2C2C2C;
                                        font-size: 16px;
                                        font-weight: 400;
                                        height: 40px;
                                        padding: 0 13px;
                                    }
                                    .lesson-balance:hover {
                                        box-shadow: 0 0 0 0.25rem rgb(13 110 253 / 25%);
                                    }
                                    @media (max-width: 400px) {    /*___ grid, md>= 768px ___*/
                                        .lesson-balance {
                                            font-size: 14px !important;
                                            padding: 0 8px !important;
                                        }
                                    }
                                </style>
                            <a href="<?= Url::to(['payment/index'], true); ?>">
                                <?php
                                    $lessons = Yii::$app->user->identity->getLessonBalance(Yii::$app->user->id);
                                ?>
                                <button class="btn lesson-balance">
                                    <?= Yii::$app->devSet->getTranslate('endDate') ?>:
                                    <?php
                                        $endDate = new DateTime('now 00:00');
                                        $endDate->modify('+'.Yii::$app->user->identity->getCpBalance().' days');
                                        $dateDifference = $endDate->diff(new DateTime('now 00:00'))->d;
                                    ?>
                                    <?php if ($dateDifference > 3) { ?>
                                        <b style="color: #00B67A;">
                                            <?= $endDate->format('d-m-Y') ?>
                                        </b>
                                    <?php } else if ($dateDifference <= 3) { ?>
                                        <b style="color: #FF3838;">
                                            <?= $endDate->format('d-m-Y') ?>
                                        </b>
                                    <?php } ?>

                                    <?php /*if ($lessons >= 3) { */?><!--
                                        <b style="color: #00B67A;"><?/*= $lessons */?></b> <?/*= Yii::$app->devSet->getTranslate('lessons') */?>
                                    <?php /*} */?>
                                    <?php /*if ($lessons < 3) { */?>
                                        <?php /*if ($lessons < 2) { */?>
                                            <b style="color: #FF3838;"><?/*= $lessons */?></b> <?/*= Yii::$app->devSet->getTranslate('lesson') */?>
                                        <?php /*} else { */?>
                                            <b style="color: #FF3838;"><?/*= $lessons */?></b> <?/*= Yii::$app->devSet->getTranslate('lessons') */?>
                                        <?php /*} */?>
                                    --><?php /*} */?>
                                </button>
                            </a>
                        <?php } else { ?>
                            <a href="<?= Url::to(['payment/index'], true); ?>">
                                <button class="btn subscribe-button">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7 0V7H0V11H7V18H11V11H18V7H11V0H7Z" fill="white"/>
                                    </svg>
                                    &nbsp;<?= Yii::$app->devSet->getTranslate('pricesButton') ?>
                                </button>
                            </a>
                        <?php } ?>
                    <?php } ?>

                    <div class="vertical-border"></div>

                    <div class="dropdown align-self-center">
                        <div class="profile-avatar text-center cursor-pointer" style="background-color: <?= (Yii::$app->user->identity->userProfile->color == null) ? '#2B82ED' : Yii::$app->user->identity->userProfile->color; ?>" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= strtoupper(substr(Yii::$app->user->identity->username, 0,1)) ?>
                        </div>

                        <div class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="dropdownMenuButton1">
                            <div class="user-initials w-100">
                                <div class="profile-avatar text-center" style="float: left;margin-right: 8px; background-color: <?= (Yii::$app->user->identity->userProfile->color == null) ? '#2B82ED' : Yii::$app->user->identity->userProfile->color; ?>;">
                                    <?= strtoupper(substr(Yii::$app->user->identity->username, 0,1)) ?>
                                </div>
                                <div style="display: flex;flex-direction: column;">
                                    <p style="font-size: 14px;line-height: 20px;color: #000000;font-weight: 600;display: inline-block; margin-bottom: 0;">
                                        <?= Yii::$app->user->identity->username ?>
                                    </p>
                                    <p class="cut-text" style="color: #979797;font-weight: 400;font-size: 13px;margin-bottom: 0;">
                                        <?= Yii::$app->user->identity->email ?>
                                    </p>
                                </div>
                            </div>

                            <div class="w-100" style="border-top: 1px solid #E0E2E7;margin-top: 5px;"></div>

                            <div class="user-settings w-100">
                                <!--<a href="<?/*= Url::to(['payment/index'], true) */?>">
                                    <p>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99414 3.98955L10.595 1.33325L12.1325 3.99622L5.99414 3.98955Z" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M1.33301 4.66667C1.33301 4.29847 1.63148 4 1.99967 4H13.9997C14.3679 4 14.6663 4.29847 14.6663 4.66667V14C14.6663 14.3682 14.3679 14.6667 13.9997 14.6667H1.99967C1.63148 14.6667 1.33301 14.3682 1.33301 14V4.66667Z" stroke="#333333" stroke-width="1.33" stroke-linejoin="round"/>
                                            <path d="M11.75 11.0001H14.6667V7.66675H11.75C10.7835 7.66675 10 8.41295 10 9.33341C10 10.2539 10.7835 11.0001 11.75 11.0001Z" stroke="#333333" stroke-width="1.33" stroke-linejoin="round"/>
                                            <path d="M14.667 5.5V13.5" stroke="#333333" stroke-width="1.33" stroke-linecap="round"/>
                                        </svg>
                                        <?/*= Yii::$app->devSet->getTranslate('balance') */?>: &nbsp;<span style="color: #00B67A;font-weight: 500;"><?/*= Yii::$app->user->identity->getCpBalance() */?> <?/*= Yii::$app->devSet->getTranslate('days') */?></span>
                                    </p>
                                </a>-->

                                <a data-popup-id="time-zone">
                                    <p>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                            <path d="M7.99967 14.6668C11.6816 14.6668 14.6663 11.682 14.6663 8.00015C14.6663 4.31825 11.6816 1.3335 7.99967 1.3335C4.31777 1.3335 1.33301 4.31825 1.33301 8.00015C1.33301 11.682 4.31777 14.6668 7.99967 14.6668Z" stroke="#333333" stroke-width="1.33"/>
                                            <path d="M2 10.3285C2.87731 10.6803 3.50887 10.6803 3.89463 10.3285C4.4733 9.80083 3.97397 8.19927 4.7844 7.75757C5.5948 7.31587 6.82953 9.2738 7.9836 8.62957C9.13763 7.9853 7.87487 6.26743 8.67573 5.571C9.4766 4.87457 10.518 5.66 10.7 4.4955C10.8821 3.33101 9.8507 3.83607 9.6528 2.73564C9.52083 2.00204 9.52083 1.61628 9.6528 1.57837" stroke="#333333" stroke-width="1.33" stroke-linecap="round"/>
                                            <path d="M9.67348 14.4501C9.04878 13.8108 8.82388 13.2165 8.99881 12.6672C9.26121 11.8434 9.69404 11.8921 9.88278 11.3827C10.0715 10.8732 9.53837 10.1477 10.7213 9.52746C11.5099 9.11396 12.5942 9.59306 13.9741 10.9647" stroke="#333333" stroke-width="1.33" stroke-linecap="round"/>
                                        </svg>
                                        <?= Yii::$app->devSet->getTranslate('timeZone') ?>
                                    </p>
                                </a>

                                <a href="https://help.dillbill.com/en" target="_blank">
                                    <p>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                            <path d="M7.99967 14.6666C9.84061 14.6666 11.5073 13.9204 12.7137 12.714C13.9201 11.5075 14.6663 9.84085 14.6663 7.99992C14.6663 6.15899 13.9201 4.49232 12.7137 3.28587C11.5073 2.07945 9.84061 1.33325 7.99967 1.33325C6.15874 1.33325 4.49207 2.07945 3.28563 3.28587C2.0792 4.49232 1.33301 6.15899 1.33301 7.99992C1.33301 9.84085 2.0792 11.5075 3.28563 12.714C4.49207 13.9204 6.15874 14.6666 7.99967 14.6666Z" stroke="#333333" stroke-width="1.33" stroke-linejoin="round"/>
                                            <path d="M8 9.54159V8.20825C9.10457 8.20825 10 7.31282 10 6.20825C10 5.10369 9.10457 4.20825 8 4.20825C6.89543 4.20825 6 5.10369 6 6.20825" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00033 12.5417C8.46056 12.5417 8.83366 12.1686 8.83366 11.7083C8.83366 11.2481 8.46056 10.875 8.00033 10.875C7.54009 10.875 7.16699 11.2481 7.16699 11.7083C7.16699 12.1686 7.54009 12.5417 8.00033 12.5417Z" fill="#333333"/>
                                        </svg>
                                        <?= Yii::$app->devSet->getTranslate('helpCenter') ?>
                                    </p>
                                </a>

                                <form method="post" action="<?= Url::to(['user/logout'], true) ?>">
                                    <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">

                                    <button type="submit" style="background: none;border: none;padding: 0;color: #333333;font-size: 15px;">
                                        <p style="margin-bottom: 0;">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16 0H0V16H16V0Z" fill="white" fill-opacity="0.01"/>
                                                <path d="M7.99723 2H2V14H8" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M11 11L14 8L11 5" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M5.33301 7.99731H13.9997" stroke="#333333" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <?= Yii::$app->devSet->getTranslate('logout') ?>
                                        </p>
                                    </button>
                                </form>
                            </div>

                            <div class="w-100" style="border-top: 1px solid #E0E2E7;"></div>

                            <div class="privacy-terms w-100">
                                <a href="https://drive.google.com/file/d/1zV9dPa0reakkCVQa4_itFM2QLFeKBCyM/view" target="_blank"><?= Yii::$app->devSet->getTranslate('privacyPolicy') ?></a> · <a href="https://drive.google.com/file/d/1WRB6RLCZkGs6VARf9-GEdLJuR9fIggok/view" target="_blank"><?= Yii::$app->devSet->getTranslate('termsOfUse') ?></a> · <a href="https://blog.dillbill.com/<?= Yii::$app->language ?>" target="_blank"><?= Yii::$app->devSet->getTranslate('blog') ?></a> · <a href="https://dillbill.com/contact-us" target="_blank"><?= Yii::$app->devSet->getTranslate('getContact') ?></a>
                                <br><span>DillBill © 2021</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>



        <?php $this->beginBody(); ?>
            <div class="middle-body"
                <?php if (Yii::$app->controller->id == 'payment') { ?>
                    style="margin-left: 0;"
                <?php } ?>
            >

                <?php if(Yii::$app->user->identity->userParameters->confirmed == 'no') { ?>
                    <div class="alert alert-warning alert-dismissible fade show w-100" role="alert" style="z-index: 9;min-height: 64px;padding: 19px 32px;background: #50bf16;border:none;border-radius: 0;">
                        <strong style="display: inline-block;font-size: 14px;color: white; font-weight: 400;margin-bottom: 3px;">
                            <?= Yii::$app->devSet->getTranslate('pleaseConfirmYourEmail') ?>&nbsp;&nbsp;
                        </strong>
                        <form style="display: inline-block;" action="<?= Url::to(['user/resend-confirm'], true) ?>" method="post">
                            <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">
                            <input type="hidden" name="verification_token" value="<?= Yii::$app->user->identity->verification_token ?>">
                            <button type="submit" class="btn" style="background-color: white;padding: 0 7px;">
                                <span style="color: #5bb525;font-weight: 500;font-size: 14px;">
                                    <?= Yii::$app->devSet->getTranslate('didNotReceiveAnEmail') ?>
                                </span>
                            </button>
                        </form>
                        <button type="button" class="btn position-absolute" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;right: 10px;">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.77778 5.9979L11.6352 2.14181C12.1216 1.65561 12.1216 0.850856 11.6352 0.364652C11.1488 -0.121551 10.3438 -0.121551 9.85744 0.364652L6 4.22075L2.14256 0.364652C1.65618 -0.121551 0.851153 -0.121551 0.36478 0.364652C-0.121593 0.850856 -0.121593 1.65561 0.36478 2.14181L4.22222 5.9979L0.36478 9.854C-0.121593 10.3402 -0.121593 11.145 0.36478 11.6312C0.616352 11.8826 0.93501 12 1.25367 12C1.57233 12 1.89098 11.8826 2.14256 11.6312L6 7.77506L9.85744 11.6312C10.109 11.8826 10.4277 12 10.7463 12C11.065 12 11.3836 11.8826 11.6352 11.6312C12.1216 11.145 12.1216 10.3402 11.6352 9.854L7.77778 5.9979Z" fill="white"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Button trigger modal -->
                    <button type="button" id="resend-confirmation" class="display-none" data-bs-toggle="modal" data-bs-target="#exampleModal" style="z-index: 9;position: relative;">
                        Launch demo modal
                    </button>

                    <?php if(Yii::$app->session->get('resendConfirmation')) { ?>
                        <script>
                            $(document).ready(function () {
                                $('#resend-confirmation').click();
                                window.location.hash = '#pop-up';
                            })
                        </script>
                        <?php Yii::$app->session->remove('resendConfirmation'); ?>
                    <?php } ?>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document" style="margin-top: 100px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        <?= Yii::$app->devSet->getTranslate('subjectConfirmEmail') ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding: 20px 24px;">
                                    <strong style="font-size: 20px;line-height: 1.5;color: #333;">
                                        <?= Yii::$app->devSet->getTranslate('confirmYourEmailAddressToContinue') ?>
                                    </strong><br>
                                    <p style="font-size: 16px;line-height: 1.5;color: #777;margin-top: 5px;">
                                        <?= Yii::$app->devSet->getTranslate('confirmationEmailHasBeenSent') ?> <b style="font-weight: 600;"><?= Yii::$app->user->identity->email ?></b>.
                                    </p>

                                    <span><?= Yii::$app->devSet->getTranslate('pleaseRememberToCheckSpamFolder') ?></span>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?= $content ?>

            </div>
        <?php $this->endBody() ?>



        <link href="<?=Yii::getAlias('@web');?>/css/dashboard/pop-up.css" rel="stylesheet">
        <script src="<?=Yii::getAlias('@web');?>/js/dashboard/pop-up.js"></script>



        <div id="google-calendar" class="cd-popup" role="alert">
            <div class="cd-popup-container border-radius-10 w-100" style="max-width: 452px;">

                <!---Your Container -->
                <div class="container my-expire" style="padding: 0 20px 20px 20px;">
                    <div class="row justify-content-around">
                        <div class="col-11" align="center">
                            <br>
                            <img alt="" src="<?=Yii::getAlias('@web');?>/img/dashboard/google-calendar-connect.svg" style="margin-top:15px;margin-bottom:22px;">
                        </div>

                        <div class="col-lg-12" align="center">
                            <h2 style="color: #202734;font-size: 25px;font-weight: 600;">
                                <?= Yii::$app->devSet->getTranslate('connectGoogleCalendar') ?>
                            </h2>
                            <div style="margin-bottom: 10px;"></div>
                        </div>

                        <div class="col-lg-12" align="left">
                            <div style="padding: 10px 0;margin-bottom: 20px;">
                                <span style="color: #646E82; font-size: 16px;display: block;">
                                    <?= Yii::$app->devSet->getTranslate('byConnectingYourGoogleCalendar') ?>
                                </span>
                                <input class="google-calendar-input"
                                       placeholder="example@gmail.com"
                                       value="<?php if(Yii::$app->user->identity->userParameters->calendarGmail == null) { ?><?= Yii::$app->user->identity->email ?><?php } else { ?><?= Yii::$app->user->identity->userParameters->calendarGmail ?><?php } ?>">
                                <code class="display-none">
                                    &nbsp;<?= Yii::$app->devSet->getTranslate('useGmail') ?>.
                                </code>
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn confirm-time w-100 <?php if(Yii::$app->user->identity->userParameters->googleCalendar == 'yes') { ?> disconnect <?php } else { ?> connect <?php } ?>">
                                <?php if(Yii::$app->user->identity->userParameters->googleCalendar == 'yes') { ?>
                                    <?= Yii::$app->devSet->getTranslate('disconnect') ?>
                                <?php } else { ?>
                                    <?= Yii::$app->devSet->getTranslate('connect') ?>
                                <?php } ?>
                                <span class="spinner-grow spinner-grow-sm display-none" role="status" aria-hidden="true"></span>
                            </button>
                            <button class="btn cancel-time w-100 not-now">
                                <?= Yii::$app->devSet->getTranslate('cancel') ?>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="cd-popup-close img-replace"><?= Yii::$app->devSet->getTranslate('close') ?></div>
            </div>
        </div>


        <div id="teacher-presentation" class="cd-popup" role="alert">
            <div class="cd-popup-container border-radius-10" style="max-width: 450px;">

                <!---Your Container -->
                <br>
                <div class="container my-expire">
                    <div class="row justify-content-around">
                        <div class="col-11" align="center">
                            <h2>Teacher:&nbsp; <span></span></h2>
                            <div style="margin-top: 20px;"></div>
                        </div>
                        <div class="col-12">
                            <div id="video">
                                <iframe id="teacher-iframe" width="100%" height="100%" style="border-radius: 10px;" src="https://www.youtube.com/embed/DnrsvVcbUxM" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="cd-popup-close img-replace"><?= Yii::$app->devSet->getTranslate('close') ?></div>
            </div>
        </div>


        <div id="proficiencyLevel" class="cd-popup <?php if(Yii::$app->user->identity->userParameters->proficiency == 'yes') { ?> is-visible <?php } ?>" role="alert">
            <div class="cd-popup-container border-radius-10 w-100" style="max-width: 390px;">

                <!---Your Container -->
                <div class="container my-expire">
                    <div class="row justify-content-center">
                        <div class="col-12" align="center">
                            <div style="margin-top: 35px;">
                                <img src="<?=Yii::getAlias('@web');?>/img/dashboard/level-climb-up.svg" alt="climbing person">
                                <br><br>
                            </div>

                            <div class="col-sm-11" align="center">
                                <span style="font-size: 16px;color: #212121;font-weight: 500;">
                                    <?= Yii::$app->devSet->getTranslate('proficiencyLevel') ?>:
                                </span>
                                <div style="margin-top: 15px"></div>
                                <div class="level-letter">
                                    <?= Yii::$app->user->identity->userParameters->currentLevel ?>
                                </div>
                                <div style="margin-top: 35px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="#0" class="cd-popup-close img-replace"><?= Yii::$app->devSet->getTranslate('close') ?></a>
            </div>
        </div>


        <div id="time-zone" class="cd-popup " role="alert">
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
                                    <button type="button" class="btn pretty-button position-relative make-payment">
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


        <script>
            window.intercomSettings = {
                api_base: "https://api-iam.intercom.io",
                app_id: "c7z1ncmm"
            };
        </script>

        <script>
            // We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/c7z1ncmm'
            (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/c7z1ncmm';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
        </script>

        


    </body>

    <?php if(Yii::$app->user->identity->userParameters->proficiency == 'yes') {
        $userParameters = UserParameters::findOne(['userId' => Yii::$app->user->id]);
        $userParameters->proficiency = 'no';
        $userParameters->save();
    } ?>

    <?php if(Yii::$app->controller->action->id == 'my-classes') { ?>
        <script src="<?=Yii::getAlias('@web');?>/js/dashboard/my-classes.js"></script>

        <?php if(Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
            <script src="<?=Yii::getAlias('@web');?>/js/dashboard/empty-classes.js"></script>
        <?php } ?>

        <?php if((Yii::$app->user->identity->userParameters->availability == null) AND (Yii::$app->user->identity->userParameters->cp > 0)) { ?>
            <script>
                $(document).ready(function () {
                    $('.reserve-room-button').click(() => {
                        $('[data-popup-id="time-availability"]').click()
                    })
                })
            </script>
        <?php } ?>

        <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') { ?>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/reconnecting-websocket/1.0.0/reconnecting-websocket.min.js"></script>

            <script>
                let _socketUrl = '<?= (Yii::$app->devSet->isLocal()) ? 'ws://localhost:8880' : 'wss://dillbill.com/ws' ?>'
                let conversation =  new WebSocket(_socketUrl)
                let _socketApiKey = '<?= sha1(Yii::$app->devSet->getDevSet('socketApiKey')) ?>'
                let _enterBefore = parseInt('<?= Yii::$app->devSet->getDevSet('enterBefore') ?>', 10)
                let _token = '<?= Yii::$app->devSet->myEncryption(Yii::$app->user->identity->verification_token, Yii::$app->devSet->getDevSet('socketApiKey')) ?>'
                let _userTimeZone = '<?= $userTimeZone ?>'
                let _localTimeZone = '<?= Yii::$app->timeZone ?>'

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

                function countDownTimer() {
                    $('.reserved').each(function(e) {
                        let classLocalDateTime = new Date($(this).data('class-start-date-time'))
                        let timeLeft = classLocalDateTime.getTime() - getCurrentLocalDateTime().getTime() - (_enterBefore * 60 * 1000)
                        timeLeft = (timeLeft < 0) ? 0 : timeLeft
                        timeLeft /= 1000

                        $(this).find('.hours').html(convertHMS(timeLeft).hour)
                        $(this).find('.minutes').html(convertHMS(timeLeft).minute)
                        $(this).find('.seconds').html(convertHMS(timeLeft).second)
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

                        if(serverResponse.action === 'enterRoom') {
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

                $('.reserve-room-button').click(function () {
                    let classId = $(this).closest('.class-column').data('class-id')
                    $(this).find('.my-spinner').removeClass('display-none')

                    conversation.send(JSON.stringify({
                        'socketApiKey': _socketApiKey,
                        'token': _token,
                        'action': 'reserve',
                        'conversation-id': classId
                    }))
                })

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
                            'action': 'enterRoom',
                            'conversation-id': chosenClass.data('class-id')
                        }))
                    }
                })

                let chosenCancelClass = false
                $('.cancel-room-button').click(function () {
                    chosenCancelClass = $(this).closest('.class-column')
                    $(this).find('.my-spinner').removeClass('display-none')
                    $('[data-bs-target="#cancel-confirm"]').click()
                })

                $('#cancel-confirm .primary-button').click(function () {
                    $('#cancel-confirm .close').click()

                    if(chosenCancelClass) {
                        conversation.send(JSON.stringify({
                            'socketApiKey': _socketApiKey,
                            'token': _token,
                            'action': 'cancel',
                            'conversation-id': chosenCancelClass.data('class-id')
                        }))
                    } else {
                        alert('No class selected')
                    }
                })
            </script>
        <?php } ?>
    <?php } ?>


</html>
<?php $this->endPage() ?>