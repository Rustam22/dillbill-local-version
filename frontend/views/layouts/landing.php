<?php

use backend\models\Review;
use backend\models\Translate;
use common\models\LoginForm;
use common\models\GoogleLogin;
use common\models\User;
use common\models\UserParameters;
use common\models\UserProfile;
use frontend\assets\BasicAppAsset;
use yii\helpers\Url;

BasicAppAsset::register($this);

$faqQuestions = Translate::find()->
select(['keyword', Yii::$app->language])->
where(['like', 'keyword', 'faqQ'])->
orderBy(['keyword' => SORT_ASC])->
asArray()->
all();

$ipCountry = Yii::$app->devSet->ip_info("Visitor", "Country");
$mobile = '+994775791000';
//$ipCountry = 'Turkey';
//debug($ipCountry);
//debug(Yii::$app->request->cookies['_language']);
//debug(Yii::$app->language);


if(Yii::$app->request->cookies['_language'] == null) {
    if($ipCountry == 'Turkey') {
        Yii::$app->getResponse()->redirect(Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'tr'], true), 301);
    }
    if($ipCountry == 'Russia') {
        Yii::$app->getResponse()->redirect(Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'ru'], true), 301);
    }
    if($ipCountry == 'Brazil') {
        Yii::$app->getResponse()->redirect(Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'pt'], true), 301);
    }
    /*if($ipCountry == 'Azerbaijan') {
        Yii::$app->getResponse()->redirect(Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'az'], true));
    }*/
}


$faqAnswers = Translate::find()->
select(['keyword', Yii::$app->language])->
where(['like', 'keyword', 'faqA'])->
orderBy(['keyword' => SORT_ASC])->
asArray()->
all();

$reviews = Review::find()
    ->select(['beforeLevel', 'afterLevel', 'stars', 'name', 'image', 'position', 'language', 'description'])
    ->where(['language' => Yii::$app->language])
    ->orderBy(['orderNumber' => SORT_DESC])
    ->asArray()
    ->all();

if(Yii::$app->controller->action->id == 'prices') {
    Yii::$app->session->set('root', 'prices');
}

// Create Google Client Request to access Google API
$client = new Google_Client();

$client->setClientId(Yii::$app->params['googleSignInClientId']);
$client->setClientSecret(Yii::$app->params['googleSignInClientSecret']);
$client->setRedirectUri(Yii::$app->devSet->isLocal() ? 'http://localhost' : 'https://dillbill.com');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    //debug($token);

    if(isset($token['access_token'])) {
        $client->setAccessToken($token['access_token']);

        // get profile info
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();

        debug($google_account_info);

        $user = User::findOne(['email' => $google_account_info->email]);

        if($user == null) {
            $user = new User();
            $userProfile = new UserProfile();
            $userParameters = new UserParameters();

            $password = Yii::$app->security->generateRandomString(6);

            $user->username = $google_account_info->name;
            $user->email = $google_account_info->email;

            $userParameters->confirmed = 'yes';
            $userParameters->currentLevel = 'empty';
            $userProfile->name = $google_account_info->givenName;
            $userProfile->surname = $google_account_info->familyName;

            $user->setPassword($password);
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();
            $user->generatePromoCode($google_account_info->givenName);

            if($user->save()) {
                $userProfile->link('user', $user);
                $userParameters->link('user', $user);

                $loginModel = new LoginForm();
                $loginModel->email = $google_account_info->email;
                $loginModel->password = $password;
                $loginModel->rememberMe = true;

                if($loginModel->login()) {
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
                                CURLOPT_POSTFIELDS =>'
                                {
                                      "to": "'.$user->email.'",
                                      "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['welcome'].'",
                                      "message_data": {
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

                    if(isset($_GET['root'])) {
                        if($_GET['root'] == 'empty') {
                            Yii::$app->response->redirect(['dashboard/my-classes']);
                        }

                        if($_GET['root'] == 'payment') {
                            Yii::$app->response->redirect(['payment/index']);
                        }
                    } else {
                        Yii::$app->response->redirect(['dashboard/my-classes']);
                    }
                } else {
                    echo '<script>alert("'.Yii::$app->devSet->getTranslate('userFailedToLogin').'");</script>';
                }
            } else {
                echo '<script>alert("'.Yii::$app->devSet->getTranslate('userFailedToSave').'");</script>';
            }
        } else {
            $loginModel = new GoogleLogin();

            $loginModel->email = $google_account_info->email;
            $loginModel->rememberMe = true;

            if($loginModel->login()) {
                if(Yii::$app->session->get('root') == 'prices') {
                    Yii::$app->session->remove('root');
                    Yii::$app->response->redirect(['payment/index']);
                } else {
                    Yii::$app->response->redirect(['dashboard/my-classes']);
                }
            } else {
                Yii::$app->session->set('authError', true);
                Yii::$app->session->set('authErrorMessage', Yii::$app->devSet->getTranslate('withoutGoogleSignIn'));

                Yii::$app->response->redirect(['landing/index']);
            }
        }
    } else {
        echo '<script>alert("'.Yii::$app->devSet->getTranslate('userFailedToLogin').'");</script>';
    }
}

$_GET['root'] = 'empty';

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" >

<head>

    <!---------     Required meta tags      --------->
    <meta charset="utf-8">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!=='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-WWXHM4Z');</script>
    <!-- End Google Tag Manager -->

    <script>
        !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on","addSourceMiddleware","addIntegrationMiddleware","setAnonymousId","addDestinationMiddleware"];analytics.factory=function(e){return function(){var t=Array.prototype.slice.call(arguments);t.unshift(e);analytics.push(t);return analytics}};for(var e=0;e<analytics.methods.length;e++){var key=analytics.methods[e];analytics[key]=analytics.factory(key)}analytics.load=function(key,e){var t=document.createElement("script");t.type="text/javascript";t.async=!0;t.src="https://cdn.segment.com/analytics.js/v1/" + key + "/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);analytics._loadOptions=e};analytics._writeKey="0x8AxE1IIioq3mJgxEGDOKeuM8ewbV6Z";;analytics.SNIPPET_VERSION="4.15.3";
            analytics.load("0x8AxE1IIioq3mJgxEGDOKeuM8ewbV6Z");
            analytics.page();
        }}();
    </script>

    <!--------------    Metadata    --------------->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

    <?php if(Yii::$app->controller->action->id == 'index') { ?>
        <title><?= Yii::$app->devSet->getTranslate('indexTitle') ?></title>

        <meta name="robots" content="index, follow">
        <meta name="description" content="<?= Yii::$app->devSet->getTranslate('indexMetaDescription') ?>">

        <!-- Facebook Open graph -->
        <meta property="og:locale" content="<?= Yii::$app->language ?>">
        <meta property="og:url" content="<?= Yii::$app->request->hostInfo ?>">
        <meta property="og:title" content="<?= Yii::$app->devSet->getTranslate('indexTitle') ?>">
        <meta property="og:description" content="<?= Yii::$app->devSet->getTranslate('indexMetaDescription') ?>">
        <meta property="og:site_name" content="DillBill">
        <meta property="og:type" content="website">
        <meta property="og:image" content="<?= Yii::$app->request->hostInfo ?>/img/seo/thumbnail.png">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:creator" content="@dillbillcom"/>
        <meta name="twitter:site" content="@dillbillcom"/>

        <!------    Canonical   ------->
        <?php if(Yii::$app->language == 'en') { ?>
            <link rel="canonical" href="<?= Yii::$app->request->hostInfo ?>">
        <?php } else { ?>
            <link rel="canonical" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language ?>">
        <?php } ?>

        <!------    Hreflang tags   ------->
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo ?>" hreflang="x-default">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo ?>" hreflang="en">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/az' ?>" hreflang="az">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/tr' ?>" hreflang="tr">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/ru' ?>" hreflang="ru">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/pt' ?>" hreflang="pt">
    <?php } ?>


    <?php if(Yii::$app->controller->action->id == 'about-us') { ?>
        <title><?= Yii::$app->devSet->getTranslate('aboutUs') ?> | DillBill</title>

        <meta name="robots" content="index, follow">
        <meta name="description" content="<?= Yii::$app->devSet->getTranslate('aboutUsMeta') ?>">

        <!-- Facebook Open graph -->
        <meta property="og:locale" content="<?= Yii::$app->language ?>">
        <meta property="og:url" content="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <meta property="og:title" content="<?= Yii::$app->devSet->getTranslate('aboutUs') ?> | DillBill">
        <meta property="og:description" content="<?= Yii::$app->devSet->getTranslate('aboutUsMeta') ?>">
        <meta property="og:site_name" content="DillBill">
        <meta property="og:type" content="website">
        <meta property="og:image" content="<?= Yii::$app->request->hostInfo ?>/img/seo/dillbill_logo.png">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:creator" content="@dillbillcom"/>
        <meta name="twitter:site" content="@dillbillcom"/>
    <?php } ?>


    <?php if(Yii::$app->controller->action->id == 'business') { ?>
        <title><?= Yii::$app->devSet->getTranslate('business') ?> | DillBill</title>

        <meta name="robots" content="index, follow">
        <meta name="description" content="<?= Yii::$app->devSet->getTranslate('businessPageMeta') ?>">

        <!-- Facebook Open graph -->
        <meta property="og:locale" content="<?= Yii::$app->language ?>">
        <meta property="og:url" content="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <meta property="og:title" content="<?= Yii::$app->devSet->getTranslate('business') ?> | DillBill">
        <meta property="og:description" content="<?= Yii::$app->devSet->getTranslate('businessPageMeta') ?>">
        <meta property="og:site_name" content="DillBill">
        <meta property="og:type" content="website">
        <meta property="og:image" content="<?= Yii::$app->request->hostInfo ?>/img/seo/dillbill_logo.png">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:creator" content="@dillbillcom"/>
        <meta name="twitter:site" content="@dillbillcom"/>
    <?php } ?>


    <?php if(Yii::$app->controller->action->id == 'prices') { ?>
        <title><?= Yii::$app->devSet->getTranslate('pricingTitle') ?></title>

        <meta name="robots" content="index, follow">
        <meta name="description" content="DillBill">

        <!-- Facebook Open graph -->
        <meta property="og:locale" content="<?= Yii::$app->language ?>">
        <meta property="og:url" content="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <meta property="og:title" content="<?= Yii::$app->devSet->getTranslate('pricingTitle') ?>">
        <meta property="og:description" content="DillBill">
        <meta property="og:site_name" content="DillBill">
        <meta property="og:type" content="website">
        <meta property="og:image" content="<?= Yii::$app->request->hostInfo ?>/img/seo/thumbnail.png">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:creator" content="@dillbillcom"/>
        <meta name="twitter:site" content="@dillbillcom"/>

        <!-- Canonical -->
        <?php if(Yii::$app->language == 'en') { ?>
            <link rel="canonical" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->controller->action->id ?>">
        <?php } else { ?>
            <link rel="canonical" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <?php } ?>

        <!-- Hreflang tags -->
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->controller->action->id ?>" hreflang="en">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/az/'.Yii::$app->controller->action->id ?>" hreflang="az">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/tr/'.Yii::$app->controller->action->id ?>" hreflang="tr">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/ru/'.Yii::$app->controller->action->id ?>" hreflang="ru">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/pt/'.Yii::$app->controller->action->id ?>" hreflang="pt">
    <?php } ?>


    <?php if(Yii::$app->controller->action->id == 'contact-us') { ?>
        <title><?= Yii::$app->devSet->getTranslate('getContact') ?> | DillBill</title>
        <meta name="robots" content="noindex, nofollow">
        <meta name="description" content="DillBill">

        <!-- Facebook Open graph -->
        <meta property="og:locale" content="<?= Yii::$app->language ?>">
        <meta property="og:url" content="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <meta property="og:title" content="<?= Yii::$app->devSet->getTranslate('getContact') ?> | DillBill">
        <meta property="og:description" content="DillBill">
        <meta property="og:site_name" content="DillBill">
        <meta property="og:type" content="website">
        <meta property="og:image" content="<?= Yii::$app->request->hostInfo ?>/img/seo/thumbnail.png">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:creator" content="@dillbillcom"/>
        <meta name="twitter:site" content="@dillbillcom"/>

        <!-- Canonical -->
        <?php if(Yii::$app->language == 'en') { ?>
            <link rel="canonical" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->controller->action->id ?>">
        <?php } else { ?>
            <link rel="canonical" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <?php } ?>

        <!-- Hreflang tags -->
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->controller->action->id ?>" hreflang="en">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/az/'.Yii::$app->controller->action->id ?>" hreflang="az">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/tr/'.Yii::$app->controller->action->id ?>" hreflang="tr">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/ru/'.Yii::$app->controller->action->id ?>" hreflang="ru">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/pt/'.Yii::$app->controller->action->id ?>" hreflang="pt">
    <?php } ?>


    <?php if(Yii::$app->controller->action->id == 'reset-password') { ?>
        <title><?= Yii::$app->devSet->getTranslate('resetPassword') ?> | DillBill</title>
        <meta name="robots" content="noindex, nofollow">
        <meta name="description" content="DillBill">

        <!-- Facebook Open graph -->
        <meta property="og:locale" content="<?= Yii::$app->language ?>">
        <meta property="og:url" content="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <meta property="og:title" content="<?= Yii::$app->devSet->getTranslate('resetPassword') ?> | DillBill">
        <meta property="og:description" content="DillBill">
        <meta property="og:site_name" content="DillBill">
        <meta property="og:type" content="website">
        <meta property="og:image" content="<?= Yii::$app->request->hostInfo ?>/img/seo/thumbnail.png">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:creator" content="@dillbillcom"/>
        <meta name="twitter:site" content="@dillbillcom"/>
    <?php } ?>


    <?php if(Yii::$app->controller->action->id == 'error') { ?>
        <title><?= Yii::$app->devSet->getTranslate('pageNotFoundTitle') ?></title>
        <meta name="robots" content="noindex, nofollow">
        <meta name="description" content="DillBill">

        <!-- Facebook Open graph -->
        <meta property="og:locale" content="<?= Yii::$app->language ?>">
        <meta property="og:url" content="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <meta property="og:title" content="<?= Yii::$app->devSet->getTranslate('pageNotFoundTitle') ?>" >
        <meta property="og:description" content="DillBill">
        <meta property="og:site_name" content="DillBill">
        <meta property="og:type" content="website">
        <meta property="og:image" content="<?= Yii::$app->request->hostInfo ?>/img/seo/thumbnail.png">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:creator" content="@dillbillcom"/>
        <meta name="twitter:site" content="@dillbillcom"/>

        <!-- Canonical -->
        <?php if(Yii::$app->language == 'en') { ?>
            <link rel="canonical" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->controller->action->id ?>">
        <?php } else { ?>
            <link rel="canonical" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->language.'/'.Yii::$app->controller->action->id ?>">
        <?php } ?>

        <!-- Hreflang tags -->
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/'.Yii::$app->controller->action->id ?>" hreflang="en">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/az/'.Yii::$app->controller->action->id ?>" hreflang="az">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/tr/'.Yii::$app->controller->action->id ?>" hreflang="tr">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/ru/'.Yii::$app->controller->action->id ?>" hreflang="ru">
        <link rel="alternate" href="<?= Yii::$app->request->hostInfo.'/pt/'.Yii::$app->controller->action->id ?>" hreflang="pt">
    <?php } ?>


    <link rel="icon" href="<?=Yii::getAlias('@web');?>/img/favicon.ico" type="image/ico">

    <!------    Google Recaptcha   ------->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!------    Google Optimize   ------->
    <script src="https://www.googleoptimize.com/optimize.js?id=OPT-KZ4TBTZ"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap');
    </style>

    <!--------------- Bootstrap Css --------------->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!---------------Bootstrap js----------->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

</head>

<script>
    let userSignedIn = true;
    let dashboardUrl = '<?= Url::to(['dashboard/my-classes'], true) ?>';
</script>

<link rel="stylesheet" href="/css/landing/index.css">



<body class="body_color">

    <!------------------------    Google Sign In and Recaptcha Starts   ------------------------>
    <script>
        var onSubmit = function(response) {
            document.getElementById("signUpForm").submit(); // send response to your backend service
        };

        $(document).ready(function () {
            $('#signUpForm').on('submit', function (e) {
                if (e.isDefaultPrevented()) {
                    // handle the invalid form...
                    //alert("validation failed");
                } else {
                    // everything looks good!
                    e.preventDefault();
                    console.log("validation success");
                    console.log(grecaptcha.execute());
                }
            });

            $('.live-chat').click(function () {
                Intercom('show')
            })
        })
    </script>
    <!------------------------    Google Sign In and Recaptcha Ends   ------------------------>


    <!----------- Login, SignUp Modal -------------------- START ------------------------------------->
    <div class="modal fade " id="logIn_SignUp"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog my-modal-dialog-width modal-dialog-centered modal-fullscreen-sm-down" style="">
            <div class="modal-content my-modal-dialog" >
                <div class="modal-header" style="border: none; padding: 0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row" style="margin-top: 4px">
                    <div class="col-6">
                        <div class="my-tab-1 signUp" style="">
                            <?= Yii::$app->devSet->getTranslate('signUP') ?>
                        </div>
                        <div class="my-tab-bottom signUp" style="">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="my-tab-1 logIn" style="">
                            <?= Yii::$app->devSet->getTranslate('login') ?>
                        </div>
                        <div class="my-tab-bottom logIn" style=""></div>
                    </div>
                </div>

                <div class="signUpBox">
                    <!------------------------    Google Sign In Starts   ------------------------>
                    <a href="<?= $client->createAuthUrl() ?>">
                        <button class="google-sign-in" style="width: 100%; text-align: center; font-weight: 600;font-size: 16px;padding: 12px 15px;line-height: 24px; color: #1A1C1F; border-radius: 8px; margin-top: 30px; box-shadow: 0px 4px 12px -4px rgba(26, 28, 31, 0.06); border: 1px solid #212327;background: #FFFFFF;">
                            <div class="row">
                                <div class="col-2">
                                    <img src="/img/landing/Google_Logo.svg" alt="google logo">
                                </div>
                                <div class="col-8 align-items-center">
                                    <?= Yii::$app->devSet->getTranslate('continueWithGoogle') ?>
                                </div>
                            </div>
                        </button>
                    </a>
                    <!------------------------    Google Sign In Ends   ------------------------>

                    <div class="col-12 d-flex justify-content-center" style="position: relative; text-align: center; font-style: normal;font-weight: 400;font-size: 15px;line-height: 24px; color: #646E82; z-index: 0; margin-top: 16px">
                        <div style=" background: white; padding: 12px 16px; width: fit-content">
                            <?= Yii::$app->devSet->getTranslate('or') ?>
                        </div>
                        <div style="width: 100%; position: absolute; top: 50%; height: 1px; background: #E0E2E7; z-index: -1"></div>
                    </div>

                    <!------------------------ Sign Up Form ------------------------>
                    <form id="signUpForm" action="<?= Url::to(['user/sign-up'], true) ?>" method="post" class="needs-validation" novalidate style="margin-top: 16px;">
                        <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">
                        <input class="form-root" type="hidden" name="root" value="empty">
                        <div class="row" >
                            <div class="col-6">
                                <input class="form-control form-control-lg"
                                       type="text"
                                       name="SignupUserForm[name]"
                                       minlength="2"
                                       placeholder="<?= Yii::$app->devSet->getTranslate('name') ?>"
                                       style="border-radius: 8px; padding: 12px 16px; font-size: 1rem; line-height: 24px"
                                       required
                                >
                            </div>
                            <div class="col-6">
                                <input class="form-control form-control-lg"
                                       type="text"
                                       name="SignupUserForm[surname]"
                                       minlength="2"
                                       placeholder="<?= Yii::$app->devSet->getTranslate('surname') ?>"
                                       style="border-radius: 8px; padding: 12px 16px; font-size: 1rem; line-height: 24px"
                                       required
                                >
                            </div>
                            <div class="col-12" style="margin-top: 16px">
                                <input class="form-control form-control-lg"
                                       type="email"
                                       name="SignupUserForm[email]"
                                       placeholder="<?= Yii::$app->devSet->getTranslate('email') ?>"
                                       style="border-radius: 8px; padding: 12px 16px; font-size: 1rem; line-height: 24px"
                                       required
                                >
                            </div>
                            <div id="show_hide_password_2" class="col-12" style="margin-top: 16px; position: relative">
                                <input class="form-control form-control-lg"
                                       id="InputPassword2"
                                       type="password"
                                       minlength="4"
                                       name="SignupUserForm[password]"
                                       placeholder="<?= Yii::$app->devSet->getTranslate('password') ?>"
                                       style="border-radius: 8px; padding: 12px 16px; font-size: 1rem; line-height: 24px"
                                       required
                                >
                                <div class="input-group-addon" style="position: absolute; right: 25px; top: 12px">
                                    <a >
                                        <i class="fa-eye-slash cursor-pointer" aria-hidden="true">
                                            <img class="Eye" src="/img/landing/Eye.svg" alt="password eye show">
                                            <img class="Slash" src="/img/landing/Hide.svg" alt="password eye close">
                                        </i>
                                    </a>
                                </div>
                            </div>

                            <!------------------------    Google Recaptcha Starts   ------------------------>
                            <div class="g-recaptcha"
                                 data-sitekey="<?= Yii::$app->params['googleRecaptchaSiteKey'] ?>"
                                 data-callback="onSubmit"
                                 data-size="invisible">
                            </div>
                            <!------------------------    Google Recaptcha Ends   ------------------------>

                            <div class="col-12">
                                <button type="submit" id="sign-in-button" class="btn btn-primary btn-lg my-btn-1" style=" border-radius: 8px; margin-top: 20px; width: 100%; text-align: center">
                                    <?= Yii::$app->devSet->getTranslate('signUP') ?>
                                </button>
                            </div>

                            <div class="12" style="font-style: normal;font-weight: 400;font-size: 14px;line-height: 24px;text-align: center;color: #646E82; margin: 16px 0 28px">
                                <?= Yii::$app->devSet->getTranslate('signUpTermsAndPrivacy') ?>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="logInBox">
                    <!------------------------    Google Sign In Starts   ------------------------>
                    <a href="<?= $client->createAuthUrl() ?>">
                        <button class="google-sign-in" style="width: 100%; text-align: center; font-weight: 600;font-size: 16px;padding: 12px 15px;line-height: 24px; color: #1A1C1F; border-radius: 8px; margin-top: 30px; box-shadow: 0px 4px 12px -4px rgba(26, 28, 31, 0.06); border: 1px solid #212327;background: #FFFFFF;">
                            <div class="row">
                                <div class="col-2">
                                    <img src="/img/landing/Google_Logo.svg" alt="google logo">
                                </div>
                                <div class="col-8 align-items-center">
                                    <?= Yii::$app->devSet->getTranslate('continueWithGoogle') ?>
                                </div>
                            </div>
                        </button>
                    </a>
                    <!------------------------    Google Sign In Ends   ------------------------>

                    <div class="col-12 d-flex justify-content-center" style="position: relative; text-align: center; font-style: normal;font-weight: 400;font-size: 15px;line-height: 24px; color: #646E82; z-index: 0; margin-top: 16px">
                        <div style=" background: white; padding: 12px 16px; width: fit-content">
                            <?= Yii::$app->devSet->getTranslate('or') ?>
                        </div>
                        <div style="width: 100%; position: absolute; top: 50%; height: 1px; background: #E0E2E7; z-index: -1"></div>
                    </div>


                    <?php if(Yii::$app->session->get('authError')) { ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger" role="alert" style="margin-top: 20px;margin-bottom: -10px;">
                                    <?= Yii::$app->session->get('authErrorMessage') ?>
                                </div>
                            </div>
                        </div>
                    <?php Yii::$app->session->remove('authError'); ?>
                    <?php Yii::$app->session->remove('authErrorMessage'); ?>
                        <script>
                            $(document).ready(function () {
                                setTimeout(function () {
                                    $('.btn-outline-secondary[data-bs-target="#logIn_SignUp"]').click();
                                    $('.logIn').click();
                                }, 550)
                            })
                        </script>
                    <?php } ?>


                    <!------------------------ Log In Form ------------------------>
                    <form id="loginForm" action="<?= Url::to(['user/login'], true) ?>" method="post" class="needs-validation" novalidate style="margin-top: 16px;">
                        <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="row" >
                            <div class="col-12" style="margin-top: 16px">
                                <input class="form-control form-control-lg"
                                       id="exampleInputEmail1"
                                       type="email"
                                       name="LoginForm[email]"
                                       placeholder="<?= Yii::$app->devSet->getTranslate('email') ?>"
                                       style="border-radius: 8px; padding: 12px 16px; font-size: 1rem; line-height: 24px;"
                                       required
                                >
                            </div>
                            <div id="show_hide_password" class="col-12" style="margin-top: 16px; position: relative">
                                <input class="form-control form-control-lg"
                                       id="InputPassword1"
                                       type="password"
                                       name="LoginForm[password]"
                                       minlength="4"
                                       placeholder="<?= Yii::$app->devSet->getTranslate('password') ?>"
                                       style="border-radius: 8px; padding: 12px 16px; font-size: 1rem; line-height: 24px;"
                                       required
                                >
                                <div class="input-group-addon cursor-pointer" style="position: absolute; right: 25px; top: 12px">
                                    <a>
                                        <i class="fa-eye-slash cursor-pointer" aria-hidden="true">
                                            <img class="Eye" src="/img/landing/Eye.svg" alt="eye show">
                                            <img class="Slash" src="/img/landing/Hide.svg" alt="eye hide">
                                        </i>
                                    </a>
                                </div>
                            </div>
                            <div style="margin-top: 15px">
                                <p style="float: right;font-weight: 400;font-size: 16px;line-height: 24px;color: #1877F2; cursor: pointer" data-bs-toggle="modal" aria-expanded="false" data-bs-target="#password_send">
                                    <?= Yii::$app->devSet->getTranslate('forgotPassword') ?>
                                </p>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg my-btn-1" style=" border-radius: 8px; margin-top: 20px; width: 100%; text-align: center">
                                    <?= Yii::$app->devSet->getTranslate('signIn') ?>
                                </button>
                            </div>
                            <div class="12" style="font-style: normal;font-weight: 400;font-size: 14px;line-height: 24px;text-align: center;color: #646E82; margin: 16px 0 28px">
                                <?= Yii::$app->devSet->getTranslate('loginTermsAndPrivacy') ?>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!----------- Login, SignUp Modal -------------------- END ------------------------------------->


    <!----------- Reset Password Modal -------------------- START ------------------------------------->
    <div class="modal fade" id="password_send"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog my-modal-dialog-width modal-dialog-centered modal-fullscreen-sm-down" style="">
            <div class="modal-content my-modal-dialog" >
                <div class="modal-header" style="border: none; padding: 0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row">
                    <form action="<?= Url::to(['user/forgot-password'], true) ?>" method="post" class="needs-validation" novalidate style="margin-top: 16px">
                        <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">
                        <div  style="text-align: center; font-size: 28px; font-weight: bold">
                            <?= Yii::$app->devSet->getTranslate('forgotPassword') ?>
                        </div>
                        <div style="text-align: center; margin-top: 8px; font-size: 18px">
                            <?= Yii::$app->devSet->getTranslate('enterTheEmailAddress') ?>
                        </div>

                        <?php if(Yii::$app->session->hasFlash('resetResponse')) { ?>
                            <script>
                                $(document).ready(function () {
                                    setTimeout(() => {
                                        $('[data-bs-target="#password_send"]').click()
                                    }, 550)
                                })
                            </script>
                            <?php if(Yii::$app->session->getFlash('resetResponse') == true) { ?>
                                <div class="font-size-14 regular" style="border: 1px solid #24BB00; border-radius: 5px; background-color: #EDFFE9; margin-top: 20px; width: 100%; line-height: 17px; position: relative; text-align: start; padding: 10px;">
                                    <strong><?= Yii::$app->devSet->getTranslate('resetEmailSent') ?></strong>
                                    <div style="margin-top: 10px;"></div>
                                    <?= Yii::$app->devSet->getTranslate('checkSpamFolder') ?>
                                </div>
                            <?php } ?>

                            <?php if(Yii::$app->session->getFlash('resetResponse') == false) { ?>
                                <div class="font-size-14 regular verifyCodeIsIncorrect" style="border: 1px solid #FF5D5D; border-radius: 5px; background-color: #FFF5F5; margin-top: 20px; width: 100%;position: relative;text-align: center;padding: 10px;">
                                    <?= Yii::$app->devSet->getTranslate('weWereUnableToSendEmail') ?>
                                </div>
                            <?php } ?>
                        <?php Yii::$app->session->destroySession('resetResponse'); } ?>

                        <div class="col-12" style="margin-top: 16px">
                            <input class="form-control form-control-lg"
                                   type="email"
                                   name="PasswordResetRequestForm[email]"
                                   placeholder="<?= Yii::$app->devSet->getTranslate('email') ?>"
                                   style="border-radius: 8px; padding: 12px 16px; font-size: 1rem; line-height: 24px"
                                   required
                            >
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg my-btn-1" style=" border-radius: 8px; margin-top: 20px; width: 100%; text-align: center">
                                <?= Yii::$app->devSet->getTranslate('submit') ?>
                            </button>
                        </div>
                        <div class="col-12" style=" margin-top: 16px; text-align: center">
                            <?= Yii::$app->devSet->getTranslate('backTo') ?>
                            <span type="button" class="logIn" data-bs-toggle="modal" data-bs-target="#logIn_SignUp" style=" font-size: 16px; font-weight: 500; padding: 4px; margin: 0; color: #0b5ed7">
                                <?= Yii::$app->devSet->getTranslate('login') ?>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!----------- Reset Password Modal -------------------- END ------------------------------------->


    <!----------- Top Navbar -------------------- START ------------------------------------->
    <div class="container-fluid notch_color padding-lr-15px">
        <div class="container max-width-1080 padding-tb-12">
            <div class="row justify-content-md-end justify-content-between margin-lr--15px">
                <div class="col-auto padding-lr-15px " style="padding-right: 11px">
                    <div class="btn-group">
                        <button type="button" class=" dropdown-toggle my-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/img/landing/Globe.svg" alt="globe" style="margin-right: 6px;">
                            <span style="margin-right: 6px; color: white">
                                <?php if(Yii::$app->language == 'en') { ?>English<?php } ?>
                                <?php if(Yii::$app->language == 'az') { ?>Azərbaycanca<?php } ?>
                                <?php if(Yii::$app->language == 'ru') { ?>Русский<?php } ?>
                                <?php if(Yii::$app->language == 'tr') { ?>Türkçe<?php } ?>
                                <?php if(Yii::$app->language == 'pt') { ?>Português<?php } ?>
                            </span>
                            <img src="/img/landing/chevron_down.svg" alt="chevron down">
                        </button>
                        <ul class="dropdown-menu my-dropdown-menu" style="z-index: 1111">
                            <li>
                                <?php if (Yii::$app->language == 'en') { ?>
                                    <a class="dropdown-item my-dropdown-item" href="<?= Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id], true) ?>">
                                        English
                                    </a>
                                <?php } else { ?>
                                    <a class="dropdown-item my-dropdown-item" href="<?= Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'en'], true) ?>">
                                        English
                                    </a>
                                <?php } ?>
                            </li>
                            <li>
                                <a class="dropdown-item my-dropdown-item" href="<?= Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'tr'], true) ?>">
                                    Türkçe
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item my-dropdown-item" href="<?= Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'az'], true) ?>">
                                    Azərbaycanca
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item my-dropdown-item" href="<?= Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'ru'], true) ?>">
                                    Русский
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item my-dropdown-item" href="<?= Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, 'language' => 'pt'], true) ?>">
                                    Português
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-auto padding-lr-15px phone-box" style="padding-left: 11px;">
                    <img src="/img/landing/Headset.svg" alt="headphone" style="margin-right: 6px">
                    <a href="tel:<?= $mobile ?>" style="color: white;"><span><?= $mobile ?></span></a>
                </div>
            </div>
        </div>
    </div>
    <!----------- Top Navbar -------------------- END ------------------------------------->


    <!----------- Navbar -------------------- START ------------------------------------->
    <?php if (Yii::$app->controller->action->id == 'index') { ?>
    <div class="container-fluid my-container-fluid body_color padding-lr-15px" style="z-index: 1000;">
    <?php } else { ?>
    <div class="container-fluid my-container-fluid body_color padding-lr-15px" style="z-index: 1000;border-bottom: 1px solid #E0E2E7;">
    <?php } ?>
        <?php if (Yii::$app->controller->action->id == 'business') { ?>

        <?php } else { ?>
            <div class="container max-width-1080 padding-tb-32 padding-navbar">
                <div class="row justify-content-between margin-lr--15px">
                    <div class="col-auto padding-lr-15px d-flex align-items-center d-block d-sm-none">
                        <div class="btn-group">
                            <div type="button" class=" my-btn" data-bs-toggle="modal" aria-expanded="false" data-bs-target="#menu">
                                <img src="/img/landing/Menu.svg" alt="hamburger menu">
                            </div>
                        </div>
                    </div>
                    <div class="col-auto padding-lr-15px d-flex align-items-center">
                        <?php if (Yii::$app->language != 'en') { ?>
                            <a href="<?= Yii::$app->request->hostInfo ?>/<?= Yii::$app->language ?>" >
                                <img src="/img/landing/DillBill_Logo.svg" alt="dillbill logo" height="32">
                            </a>
                        <?php } else { ?>
                            <a href="<?= Yii::$app->request->hostInfo ?>" >
                                <img src="/img/landing/DillBill_Logo.svg" alt="dillbill logo" height="32">
                            </a>
                        <?php } ?>
                    </div>


                    <div class="col-auto padding-lr-15px d-flex align-items-center d-none d-sm-block">
                        <?php if (Yii::$app->user->isGuest) { ?>
                            <a href="<?= Url::to(['landing/prices'], true) ?>" style="margin-right: 32px; color: #212327">
                                <span>
                                    <?= Yii::$app->devSet->getTranslate('pricing') ?>
                                </span>
                            </a>
                            <a href="https://kids.dillbill.com/<?= Yii::$app->language ?>" target="_blank" style="margin-right: 32px; color: #212327">
                                <span>
                                    <?= Yii::$app->devSet->getTranslate('forKids') ?>&nbsp;
                                    <b style="background: linear-gradient(66.71deg, #B63CF0 18.07%, #7415D7 84.1%);border-radius: 4px;color: white;font-size: 11px;font-weight: 600;padding: 0 5px 2px 5px;text-transform: lowercase;"><?= Yii::$app->devSet->getTranslate('new') ?></b>
                                </span>
                            </a>

                            <button type="button" class="btn btn-outline-secondary border-radius-5px logIn " data-bs-toggle="modal" data-bs-target="#logIn_SignUp" style="margin-right: 13px; font-size: 16px; font-weight: 500;">
                                <?= Yii::$app->devSet->getTranslate('login') ?>
                            </button>
                            <button type="button" class="btn btn-outline-primary border-radius-5px signUp" data-bs-toggle="modal" data-bs-target="#logIn_SignUp" style=" font-size: 16px; font-weight: 500;">
                                <?= Yii::$app->devSet->getTranslate('signUP') ?>
                            </button>
                        <?php } else { ?>
                            <div class="submit-section log-out" style="position: relative;">
                                <a style="display: flex; flex-direction: row; align-items: center; padding-left: 6px; padding-right: 6px;" class="nav-link dropdown-toggle my-dropdown-toggle h6-400" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div style="width: 32px; height: 32px;border-radius: 50%; margin-right: 10px; overflow: hidden;">
                                        <img src="/img/landing/avatar.svg" alt="avatar">
                                    </div>
                                    <div class="grays_900 h6-400" style=" margin-right: 8px;">
                                        <?= Yii::$app->user->identity->username ?>
                                    </div>
                                </a>
                                <ul class="dropdown-menu lang-dropdown-menu border-radius-10 logout-menu" aria-labelledby="navbarDropdown2" style="width: 220px; text-align: start; padding: 11px 15px">
                                    <li>
                                        <a href="<?= Url::to(['dashboard/my-classes'], true); ?>" class="dropdown-item h6-500 grays_900" style="padding: 6px 12px;">
                                            <?= Yii::$app->devSet->getTranslate('goToDashboard') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://help.dillbill.com/<?= (Yii::$app->language == 'az') ? 'tr' : Yii::$app->language ?>" target="_blank" class="dropdown-item h6-500 grays_900" style="padding: 6px 12px;">
                                            <?= Yii::$app->devSet->getTranslate('helpCenter') ?>
                                        </a
                                    </li>
                                    <li>
                                        <form action="<?= Url::to(['user/logout'], true) ?>" method="post">
                                            <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->csrfToken; ?>">
                                            <button type="submit" style="border: none; background: none;padding: 0;width: 100%;text-align: left;">
                                                <a class="dropdown-item h6-500" style="color: #FF3838; padding: 6px 12px;">
                                                    <?= Yii::$app->devSet->getTranslate('logout') ?>
                                                </a>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-auto padding-lr-15px d-flex align-items-center d-block d-sm-none">
                        <div style="border-radius: 50%;cursor: pointer">
                            <img src="/img/landing/avatar.svg" alt="profile avatar" data-bs-target="#logIn_SignUp" data-bs-toggle="modal">
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <!----------- Navbar -------------------- END ------------------------------------->


    <!-----------  Mobile Navbar -------------------- START ------------------------------------->
    <div class="modal show" id="menu"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 1059 !important;">
        <div class="modal-dialog my-modal-dialog-width modal-dialog-centered modal-fullscreen-sm-down" style="">
            <div class="modal-content my-modal-dialog justify-content-between" style="padding: 0 15px">
                <div>
                    <div class="modal-header justify-content-between" style="border: none; padding: 26px 0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin: 0"></button>
                        <?php if (Yii::$app->language != 'en') { ?>
                            <a href="<?= Yii::$app->request->hostInfo ?>/<?= Yii::$app->language ?>" >
                                <img src="/img/landing/DillBill_Logo.svg" alt="dillbill logo" height="32">
                            </a>
                        <?php } else { ?>
                            <a href="<?= Yii::$app->request->hostInfo ?>" >
                                <img src="/img/landing/DillBill_Logo.svg" alt="dillbill logo" height="32">
                            </a>
                        <?php } ?>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin: 0; visibility: hidden">
                        </button>
                    </div>
                    <div class="text-style-8" style="margin-top: 29px">
                        <!--<div style="padding: 12px 16px;">
                            <a href="#">
                                Nasıl Çalışır?
                            </a>
                        </div>-->
                        <div style="padding: 12px 16px;">
                            <a href="<?= Url::to(['landing/prices'], true) ?>">
                                <?= Yii::$app->devSet->getTranslate('pricing') ?>
                            </a>
                        </div>
                        <div style="padding: 12px 16px;">
                            <a href="https://kids.dillbill.com/<?= Yii::$app->language ?>" target="_blank" style="margin-right: 32px; color: #212327">
                                <?= Yii::$app->devSet->getTranslate('forKids') ?>
                                <b style="background: linear-gradient(66.71deg, #B63CF0 18.07%, #7415D7 84.1%);border-radius: 4px;color: white;font-size: 11px;font-weight: 600;padding: 0 5px 2px 5px;text-transform: lowercase;"><?= Yii::$app->devSet->getTranslate('new') ?></b>
                            </a>
                        </div>
                        <div style="padding: 12px 16px;">
                            <a href="<?= Url::to(['landing/business'], true) ?>">
                                <?= Yii::$app->devSet->getTranslate('business') ?>
                            </a>
                        </div>
                        <!--<div style="padding: 12px 16px;">
                            <a href="#">
                                Sıkca sorulan sorular
                            </a>
                        </div>-->
                        <div style="padding: 12px 16px;">
                            <a class="d-flex align-items-center" href="<?= Url::to(['landing/about-us'], true) ?>">
                                <?= Yii::$app->devSet->getTranslate('aboutUs') ?>
                            </a>
                        </div>
                        <div style="padding: 12px 16px;">
                            <a class="d-flex align-items-center" href="<?= Url::to(['landing/contact-us'], true) ?>">
                                <?= Yii::$app->devSet->getTranslate('getContact') ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row m-0">
                    <button type="button" class="btn btn-outline-secondary border-radius-5px logIn " data-bs-toggle="modal" data-bs-target="#logIn_SignUp" style="margin-right: 13px; font-size: 16px; font-weight: 500; padding: 12px; margin: 0;">
                        <?= Yii::$app->devSet->getTranslate('login') ?>
                    </button>
                    <button type="button" class="btn btn-outline-primary border-radius-5px signUp" data-bs-toggle="modal" data-bs-target="#logIn_SignUp" style=" font-size: 16px; font-weight: 500; padding: 12px; margin: 12px 0 44px">
                        <?= Yii::$app->devSet->getTranslate('signUP') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-----------  Mobile Navbar -------------------- END ------------------------------------->


    <?php $this->beginBody() ?>
        <?= $content ?>
    <?php $this->endBody() ?>


    <!----------- Footer -------------------- START ------------------------------------->
    <div class="container-fluid " style="padding: 0 15px">
        <div class="container max-width-1080 " style="border-top: 1px solid #E0E2E7; padding: 100px 0">
            <div class="row" style="row-gap: 55px">
                <div class=" d-flex align-items-center">
                    <?php if (Yii::$app->language != 'en') { ?>
                        <a href="<?= Yii::$app->request->hostInfo ?>/<?= Yii::$app->language ?>" >
                            <img src="/img/landing/DillBill_Logo.svg" alt="dillbill logo" height="32">
                        </a>
                    <?php } else { ?>
                        <a href="<?= Yii::$app->request->hostInfo ?>" >
                            <img src="/img/landing/DillBill_Logo.svg" alt="dillbill logo" height="32">
                        </a>
                    <?php } ?>
                </div>
                <div class="col-md-4  col-lg-3 col-6">
                    <h3 class="text-style-7"><?= Yii::$app->devSet->getTranslate('company') ?></h3>
                    <ul class="footer-ul">
                        <li >
                            <a class="d-flex align-items-center" href="<?= Url::to(['landing/about-us'], true) ?>">
                                <?= Yii::$app->devSet->getTranslate('aboutUs') ?>
                            </a>
                        </li>
                        <li >
                            <a class="d-flex align-items-center" href="https://blog.dillbill.com/<?= Yii::$app->language ?>">
                                <?= Yii::$app->devSet->getTranslate('blog') ?>
                            </a>
                        </li>
                        <!--<li >
                            <a class="d-flex align-items-center" href="#">
                                İşe alıyoruz! 🚀
                            </a>
                        </li>-->
                        <!--<li >
                            <a class="d-flex align-items-center" href="#">
                                Öğretmen ol
                            </a>
                        </li>-->
                    </ul>
                </div>
                <div class="col-md-4  col-lg-3 col-6">
                    <h3 class="text-style-7"><?= Yii::$app->devSet->getTranslate('platform') ?></h3>
                    <ul class="footer-ul">
                        <li>
                            <a class="d-flex align-items-center" href="<?= Url::to(['landing/business'], true) ?>">
                                <?= Yii::$app->devSet->getTranslate('forCorporation') ?>
                            </a>
                        </li>
                        <li >
                            <a class="d-flex align-items-center" href="<?= Url::to(['landing/prices'], true) ?>">
                                <?= Yii::$app->devSet->getTranslate('pricing') ?>
                            </a>
                        </li>
                        <!--<li ><a class="d-flex align-items-center" href="#">Nasıl çalışır?</a></li>-->
                        <!--<li ><a class="d-flex align-items-center" href="#">Çoçuklar için</a></li>-->
                    </ul>
                </div>
                <div class="col-md-4  col-lg-3 col-6">
                    <h3 class="text-style-7"><?= Yii::$app->devSet->getTranslate('support') ?></h3>
                    <ul class="footer-ul">
                        <!--<li ><a class="d-flex align-items-center" href="#">Öğrenci indirimi</a></li>-->
                        <li>
                            <a class="d-flex align-items-center" href="<?= Url::to(['landing/contact-us'], true) ?>">
                                <?= Yii::$app->devSet->getTranslate('getContact') ?>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center" href="https://help.dillbill.com/<?= (Yii::$app->language == 'az') ? 'tr' : Yii::$app->language ?>">
                                <?= Yii::$app->devSet->getTranslate('helpCenter') ?>
                            </a>
                        </li>
                        <!--<li ><a class="d-flex align-items-center" href="#">DillBill Elçisi ol</a></li>-->
                    </ul>
                </div>
                <div class="col-md-4  col-lg-3 col-6">
                    <h3 class="text-style-7"><?= Yii::$app->devSet->getTranslate('followUs') ?></h3>
                    <ul class="footer-ul">
                        <li>
                            <a class="d-flex align-items-center" href="https://www.youtube.com/channel/UCuMvD5PdXEn9WEGQZP8_mJg" target="_blank">
                                <img src="/img/landing/YouTube.svg" alt="youtube icon">Youtube
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center" href="https://www.instagram.com/dillbillcom/?hl=en" target="_blank">
                                <img src="/img/landing/Instagram.svg" alt="instagram icon">Instagram
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center" href="https://www.facebook.com/dillbillcom" target="_blank">
                                <img src="/img/landing/Facebook.svg" alt="facebook icon">Facebook
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div style="width: 100%; height: 1px; background: #E0E2E7; margin-top: 48px "></div>
            <div class="row justify-content-between" >
                <div class="col-auto" style="margin-top: 24px">
                    <?= Yii::$app->devSet->getTranslate('allRightsPreserved') ?>
                </div>
                <div class="col-auto d-flex align-items-center" style="margin-top: 24px">
                    <div class="privacy">
                        <a  href="https://drive.google.com/file/d/1WRB6RLCZkGs6VARf9-GEdLJuR9fIggok/view" style="color: #202734; margin-right: 10px; font-weight: 600">
                            <?= Yii::$app->devSet->getTranslate('termsOfUse') ?>
                        </a>
                        <a href="https://drive.google.com/file/d/1WRB6RLCZkGs6VARf9-GEdLJuR9fIggok/view" style="color: #202734; font-weight: 600">
                            <?= Yii::$app->devSet->getTranslate('privacyPolicy') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!----------- Footer -------------------- END ------------------------------------->


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script>
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            let forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                        $(".my-tab-1.logIn, .my-tab-1.Signup").click(function (){
                            form.classList.remove('was-validated');
                        })

                    }, false)
                })

        })()


        $('.brand-carousel').owlCarousel({
            loop:true,
            margin:30,
            autoplay:true,
            smartSpeed: 2000,
            responsive:{
                0:{
                    items:3
                },
                600:{
                    items:3
                },
                1000:{
                    items:4
                }
            }
        })
        $('.brand-carousel')._speed= 100
    </script>
    <script src="/js/landing/main.js"></script>


</body>



<?php $this->endPage() ?>
