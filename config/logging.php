<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'customlog' => [
            'driver' => 'single',
            'path' => storage_path('logs/custom.log'),
            'level' => 'info',
        ],
        'travelpath' => [
            'driver' => 'single',
            'path' => storage_path('logs/travelpath.log'),
            'level' => 'debug',
        ],

        'mailQueueError' => [
            'driver' => 'single',
            'path' => storage_path('logs/mailQueueError.log'),
            'level' => 'debug',
        ],

        'apiError' => [
            'driver' => 'single',
            'path' => storage_path('logs/apiError.log'),
            'level' => 'debug',
        ],

        'moduleEntriesLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/moduleEntriesLog.log'),
            'level' => 'debug',
        ],

        'zipFileDeleteLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/zipFileDeleteLog.log'),
            'level' => 'debug',
        ],

        'landingPageCustomerSearchSession' => [
            'driver' => 'single',
            'path' => storage_path('logs/landingPageCustomerSearchSession.log'),
            'level' => 'info',
        ],
        'reportLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/reportLog.log'),
            'level' => 'debug',
            'permission' => 0660,
        ],
        'timeSheetApprovalRatingLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/timeSheetApprovalRatingLog.log'),
            'level' => 'debug',
            'permission' => 0660,
        ],
        'fileDeleteJobLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/fileDeleteJobLog.log'),
            'level' => 'debug',

        ],
        'matchScoreLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/matchScoreLog.log'),
            'level' => 'debug',
        ],

        'motionSensor' => [
            'driver' => 'single',
            'path' => storage_path('logs/motionSensorLog.log'),
            'level' => 'debug',

        ],
        'timesheetApproval' => [
            'driver' => 'single',
            'path' => storage_path('logs/timesheetApprovalLog.log'),
            'level' => 'debug',

        ],
        'kpiLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/kpi.log'),
            'level' => 'info',
        ],
        'googleApi' => [
            'driver' => 'single',
            'path' => storage_path('logs/googleApiLog.log'),
            'level' => 'debug',

        ],
        'osgcPayment' => [
            'driver' => 'daily',
            'path' => storage_path('logs/osgc/osgcPayment.log'),
            'level' => 'debug',

        ],
        'idsPayment' => [
            'driver' => 'daily',
            'path' => storage_path('logs/ids/idsPayment.log'),
            'level' => 'debug',
            'permission' => 0660,
        ],
        'contractExpiryReminderLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/contractExpiryReminderLog.log'),
            'level' => 'info',
        ],
        'summaryDashboardLog' => [
            'driver' => 'single',
            'path' => storage_path('logs/summaryDashboard.log'),
            'level' => 'info',
        ],
    ],

];
