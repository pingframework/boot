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

namespace Pingframework\Boot\Http\Middleware;

use Pingframework\Boot\Http\Routing\Jrpc\JrpcRequestRootSchema;
use Pingframework\Boot\Http\Routing\Jrpc\JrpcResponseRootSchema;
use Pingframework\Boot\Http\Routing\Jrpc\JrpcRouteDefinition;
use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class JrpcRequestMethodContext extends JrpcRequestContext
{
    public function __construct(
        Request                                $request,
        Response                               $response,
        public readonly JrpcRequestRootSchema  $requestRootSchema,
        public readonly JrpcResponseRootSchema $responseRootSchema,
        public readonly JrpcRouteDefinition    $routeDefinition,
        array                                  $data = [],
    ) {
        parent::__construct($request, $response, $data);
    }
}