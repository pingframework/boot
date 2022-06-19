<?php

namespace Pingframework\Boot\Http\Routing;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

interface SlimRequestHandlerInterface
{
    public function handle(
        RouteDefinition $rd,
        Request         $request,
        Response        $response,
        array           $args
    ): ResponseInterface;
}