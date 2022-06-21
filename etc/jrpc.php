<?php

use Pingframework\Boot\Http\Server\JrpcSwooleHttpRequestHandler;

return [
    JrpcSwooleHttpRequestHandler::CONFIG_JRPC_METHOD         => 'POST',
    JrpcSwooleHttpRequestHandler::CONFIG_JRPC_URI            => '/jrpc',
    JrpcSwooleHttpRequestHandler::CONFIG_JRPC_DISPLAY_ERRORS => false,
];