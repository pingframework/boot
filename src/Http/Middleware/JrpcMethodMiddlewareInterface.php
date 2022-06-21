<?php

namespace Pingframework\Boot\Http\Middleware;

interface JrpcMethodMiddlewareInterface
{
    public function handle(JrpcRequestMethodContext $ctx): void;
}