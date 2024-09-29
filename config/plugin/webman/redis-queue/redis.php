<?php
return [
    'default' => [
        'host' => 'redis://redis:6379',
        'options' => [
            'auth' => null,
            'db' => 0,
            'prefix' => '',
            'max_attempts'  => 5,
            'retry_seconds' => 5,
        ]
    ],
];
