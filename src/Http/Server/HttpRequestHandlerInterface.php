<?php

namespace Pingframework\Boot\Http\Server;

use Swoole\Http\Request;
use Swoole\Http\Response;

interface HttpRequestHandlerInterface
{
    public function handle(Request $request, Response $response): void;
}