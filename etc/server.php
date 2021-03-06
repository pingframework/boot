<?php

use Pingframework\Boot\Application\HttpServerApplication;

return [
    HttpServerApplication::CONFIG_SERVER => [
        // Listen on
        HttpServerApplication::CONFIG_SERVER_BIND_HOST => '127.0.0.1',
        HttpServerApplication::CONFIG_SERVER_BIND_PORT => 8080,

        // Server
        'reactor_num'                                  => 8,
        'worker_num'                                   => 2,
        'discard_timeout_request'                      => true,

        // Worker
        'max_request'                                  => 0,
        'max_request_grace'                            => 0,

        // Logging
        'log_level'                                    => 1,
        'log_date_format'                              => '%Y-%m-%d %H:%M:%S',
        'log_date_with_microseconds'                   => false,

        // Enable trace logs
        'trace_flags'                                  => SWOOLE_TRACE_ALL,

        // Compression
        'http_compression'                             => true,
        'http_compression_level'                       => 3, // 1 - 9
        'compression_min_length'                       => 20,

        // HTTP Server
        'http_parse_post'                              => true,
        'http_parse_cookie'                            => true,
        'upload_tmp_dir'                               => '/tmp',
    ]
];