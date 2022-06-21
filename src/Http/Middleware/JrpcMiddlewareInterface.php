<?php

namespace Pingframework\Boot\Http\Middleware;

interface JrpcMiddlewareInterface
{
    public function handle(JrpcRequestContext $ctx): void;
}