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

namespace Pingframework\Boot\Annotations;

use Attribute;
use Pingframework\Boot\Annotations\AttributeScanner\AttributeScannerResultSet;
use Pingframework\Boot\Annotations\Composition\AutowireDefinitionFinderTrait;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Http\Routing\RouteRegistry;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Controller implements MethodDefinitionRegistrar
{
    use AutowireDefinitionFinderTrait;

    public readonly array $ignoreMiddleware;

    public function __construct(
        public readonly string $pattern,
        public readonly array  $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        string                 ...$ignoreMiddleware,
    ) {
        $this->ignoreMiddleware = $ignoreMiddleware;
    }

    /**
     * Register definition in the dependency container.
     *
     * @param AttributeScannerResultSet    $rs
     * @param DependencyContainerInterface $c
     * @param ReflectionClass              $rc
     * @param ReflectionMethod             $rm
     * @return void
     * @throws DependencyContainerException
     */
    public function registerMethod(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionMethod             $rm,
    ): void {
        $this->findAutowire($c, new ReflectionClass(RouteRegistry::class))->method('add', [
            DI::value($rc->getName()),
            DI::value($rm->getName()),
            DI::value($this->pattern),
            DI::value($this->methods),
            DI::value($this->findGroupPattern($rc)),
            DI::value($this->ignoreMiddleware),
        ]);
    }

    private function findGroupPattern(ReflectionClass $rc): ?string
    {
        foreach ($rc->getAttributes(ControllerGroup::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            return $attribute->getArguments()[0];
        }

        return null;
    }
}