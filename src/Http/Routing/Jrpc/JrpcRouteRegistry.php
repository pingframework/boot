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


use Pingframework\Boot\Annotations\Service;
use RuntimeException;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service]
class JrpcRouteRegistry
{
    /**
     * @var array<string, JrpcRouteDefinition>
     */
    private array $routes = [];

    public function getRoute(string $method): JrpcRouteDefinition
    {
        if (!isset($this->routes[$method])) {
            throw new RuntimeException(sprintf('Json RPC method "%s" not found', $method));
        }

        return $this->routes[$method];
    }

    public function add(
        string $className,
        string $methodName,
        string $method,
        array  $ignoreMiddlewares = []
    ): void {
        $this->routes[$method] = new JrpcRouteDefinition($className, $methodName, $method, $ignoreMiddlewares);
    }
}