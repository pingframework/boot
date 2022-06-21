<?php

/**
 * Ping Boot
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * Json RPC://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phpsuit.net so we can send you a copy immediately.
 *
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Boot\Http\Routing\Jrpc;

use Pingframework\Boot\Annotations\JrpcMiddleware;
use Pingframework\Boot\Http\Middleware\JrpcMethodMiddlewareRegistry;
use Pingframework\Boot\Http\Middleware\JrpcMiddlewareInterface;
use Pingframework\Boot\Http\Middleware\JrpcRequestContext;
use Pingframework\Boot\Http\Middleware\JrpcRequestMethodContext;
use Pingframework\Boot\Utils\Json\JsonDecoderInterface;
use Pingframework\Boot\Utils\ObjectMapper\ObjectMapper;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[JrpcMiddleware]
class JrpcRouter implements JrpcMiddlewareInterface
{
    public function __construct(
        public readonly JrpcMethodMiddlewareRegistry $methodMiddlewareRegistry,
        public readonly JrpcRouteRegistry            $routeRegistry,
        public readonly ObjectMapper                 $objectMapper,
        public readonly JsonDecoderInterface         $jsonDecoder,
        public readonly JrpcRequestHandlerInterface  $requestHandler,
        public readonly LoggerInterface              $logger,
    ) {}

    public function handle(JrpcRequestContext $ctx): void
    {
        $requestArray = $this->jsonDecoder->unmarshal($ctx->request->getContent());
        $isSingleRequest = array_key_exists('jsonrpc', $requestArray);
        if ($isSingleRequest) {
            $requestArray = [$requestArray];
        }

        $requests = $this->objectMapper->mapListFromArray($requestArray, JrpcRequestRootSchema::class);
        $responses = [];

        foreach ($requests as $requestRootSchema) {
            $responseRootSchema = new JrpcResponseRootSchema();
            $responses[] = $responseRootSchema;
            $responseRootSchema->id = $requestRootSchema->id;

            try {
                $rd = $this->routeRegistry->getRoute($requestRootSchema->method);
                $jrpcCtx = new JrpcRequestMethodContext(
                    $ctx->request,
                    $ctx->response,
                    $requestRootSchema,
                    $responseRootSchema,
                    $rd,
                    $ctx->data
                );

                foreach ($this->methodMiddlewareRegistry->middlewares as $middleware) {
                    $middleware->handle($jrpcCtx);
                }

                $this->requestHandler->handle($rd, $jrpcCtx);
            } catch (Throwable $e) {
                $this->logger->error(
                    sprintf(
                        "JRPC error: %s. in file: %s(%s). Stack trace: \n%s",
                        $e->getMessage(),
                        $e->getFile(),
                        $e->getLine(),
                        $e->getTraceAsString()
                    )
                );
                $responseRootSchema->error = JrpcResponseRootErrorSchema::fromException($e);
            }
        }

        if ($isSingleRequest) {
            $ctx->response->write($this->objectMapper->unmapToJson($responses[0] ?? []));
        } else {
            $ctx->response->write($this->objectMapper->unmapListToJson($responses));
        }
    }
}