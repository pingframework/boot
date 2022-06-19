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

namespace Pingframework\Boot\Http\Routing;

use Pingframework\Boot\Annotations\Service;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service]
class RouteRegistry
{
    /**
     * @var RouteDefinition[]
     */
    private array $routes = [];

    /**
     * @return RouteDefinition[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array<string, RouteDefinition[]>
     */
    public function getGroupedRoutes(): array
    {
        $routes = [];

        foreach ($this->routes as $route) {
            if ($route->groupPattern !== null) {
                $routes[$route->groupPattern][] = $route;
            }
        }

        return $routes;
    }

    /**
     * @return RouteDefinition[]
     */
    public function getUngroupedRoutes(): array
    {
        $routes = [];

        foreach ($this->routes as $route) {
            if ($route->groupPattern === null) {
                $route[] = $route;
            }
        }

        return $routes;
    }

    public function add(
        string  $className,
        string  $methodName,
        string  $pattern,
        array   $httpMethods,
        ?string $groupPattern,
        array   $ignoreMiddlewares
    ): void {
        $this->routes[] = new RouteDefinition(
            $className,
            $methodName,
            $pattern,
            $httpMethods,
            $groupPattern,
            $ignoreMiddlewares
        );
    }
}