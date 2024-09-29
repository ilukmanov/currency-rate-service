<?php
return [
    'consumer'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            'consumer_dir' => app_path() . '/queue/redis'
        ]
    ]
];