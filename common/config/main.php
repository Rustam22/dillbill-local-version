<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'pusher' => [
            'class' => 'common\components\PusherComponent',

            // Mandatory parameters.
            'appId' => '993594',
            'appKey' => '763eba7618a888cb7055',
            'appSecret' => 'e9ce14d65e21ba4d5cf9',

             // Optional parameters.
            'options' => ['useTLS' => true, 'cluster' => 'ap2']
        ],

        'googleCalendar' => [
            'class' => 'common\components\GoogleCalendarComponent',
        ],

        'acc' => [
            'class' => 'common\components\AutomaticClassCreationComponent',
        ],

        'devSet' => [
            'class' => 'common\components\DeveloperSettingsComponent',
        ]
    ],
];
