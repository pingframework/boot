<?php

namespace Pingframework\Boot\Http\Routing\Jrpc;

use Pingframework\Boot\Http\Middleware\JrpcRequestMethodContext;

interface JrpcRequestHandlerInterface
{
    public function handle(
        JrpcRouteDefinition      $rd,
        JrpcRequestMethodContext $ctx,
    ): void;
}