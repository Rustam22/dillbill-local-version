<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

use \yii\web\Request;

$baseUrl = str_replace('/frontend/web', '', (new Request)->getBaseUrl());


return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'landing/index',

    'language' => 'en',
    //'sourceLanguage' => 'en',

    'controllerNamespace' => 'frontend\controllers',

    'components' => [

        'captcha' => [
            'name'    => 'captcha',
            'class'   => 'pctux\recaptcha\InvisibleRecaptcha',
            'siteKey' => '',
            'secret'  => ''
        ],


        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => $baseUrl,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */


        /************************  My Codes ************************/

        'i18n' => [
            'translations' => [  // Translation folder
                'app*' => [      // The pattern app* indicates that all message categories whose names start with app should be translated using this message source.
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/translations',
                ],

                'backend' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/translations',
                ],

                'frontend' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/translations',
                ],
            ],
        ],

        // Override the urlManager component
        'urlManager' => [
            'class' => 'codemix\localeurls\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableLanguageDetection' => false,
            'enableStrictParsing' => true,
            //'enableDefaultLanguageUrlCode' => true,
            //'enableLanguagePersistence' => false,
            // List all supported languages here
            // Make sure, you include your app's default language.
            'languages' => ['en', 'ru', 'az', 'tr', 'pt'],
            'rules' => [
                // Landing Part
                '' => 'landing/index',
                //'general-english' => 'landing/general-english',
                //'intensive-english' => 'landing/intensive-english',
                //'english-conversation' => 'landing/english-conversation',
                'about-us' => 'landing/about-us',
                'contact-us' => 'landing/contact-us',
                'business' => 'landing/business',
                'prices' => 'landing/prices',
                'error' => 'site/error',

                // User Login, Reset Password, SignUP and etc.
                'login' => 'user/login',
                'logout' => 'user/logout',
                'sign-up' => 'user/sign-up',
                'google-sign-in' => 'user/google-sign-in',
                'confirm-email' => 'user/confirm-email',
                'resend-confirm' => 'user/resend-confirm',
                'forgot-password' => 'user/forgot-password',
                'reset-password' => 'user/reset-password',

                // Payment part
                'payment' => 'payment/index',
                'checkout' => 'payment/checkout',
                'generate-checkout' => 'payment/generate-checkout',
                'payment-intent' => 'payment/payment-intent',
                'POST webhook' => 'payment/webhook',
                'promo' => 'payment/promo',
                'card' => 'payment/card',
                'call-back' => 'payment/call-back',

                // Dashboard Part
                'dashboard' => 'dashboard/dashboard',
                'my-classes' => 'dashboard/my-classes',
                'level-test' => 'dashboard/level-test',
                'time-availability' => 'dashboard/time-availability',
                'grammar' => 'dashboard/grammar',
                'timeZoneAssign' => 'dashboard/time-zone-assign',
                'google-calendar' => 'dashboard/google-calendar',
                'confirm-start-date' => 'dashboard/confirm-start-date',
                'confirm-phone-number' => 'dashboard/confirm-phone-number',
                'class-history' => 'dashboard/class-history',
                'boarding' => 'dashboard/boarding',
                'feedback-confirm' => 'dashboard/feedback-confirm',

                // APIs Gateway
                'POST tutor-assign' => 'api/tutor-assign',
                'POST tutor-cancel' => 'api/tutor-cancel',
                'POST trial-tutor-assign' => 'api/trial-tutor-assign',
                'POST trial-tutor-cancel' => 'api/trial-tutor-cancel',
                'POST change-level' => 'api/change-level'
            ],

            // Ignore / Filter route pattern's
            'ignoreLanguageUrlPatterns' => [

            ],
        ],

    ],
    'params' => $params,
];

