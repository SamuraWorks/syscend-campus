<?php

return [

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'maxProcesses' => 10,
                'maxTime' => 3600,
                'maxJobs' => 1000,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 90,
                'nice' => 0,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'maxProcesses' => 3,
                'maxTime' => 3600,
                'maxJobs' => 1000,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 90,
                'nice' => 0,
            ],
        ],
    ],

    'default' => env('HORIZON_PREFIX', 'horizon:'),

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    'waits' => [
        'redis' => 60,
    ],

    'trim' => [
        'recent' => 24 * 60,
        'failed' => 24 * 60,
        'rejected' => 24 * 60,
        'monitored' => 24 * 60,
    ],

    'metrics' => [
        'trim_job_metrics_using' => 'created_at',
        'trim_monitor_metrics_using' => 'created_at',
    ],

    'middleware' => ['web'],

    'redact' => [],

    'extensions' => [
        'metrics' => false,
    ],

];
