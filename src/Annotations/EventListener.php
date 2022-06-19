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
use LogicException;
use Pingframework\Boot\Annotations\AttributeScanner\AttributeScannerResultSet;
use Pingframework\Boot\Annotations\Composition\AutowireDefinitionFinderTrait;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Event\EventDispatcher;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use Pingframework\Boot\Utils\Priority;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_METHOD)]
class EventListener implements MethodDefinitionRegistrar
{
    use AutowireDefinitionFinderTrait;

    public function __construct(
        public readonly int $priority = Priority::NORMAL,
    ) {}

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
        $this->findAutowire($c, new ReflectionClass(EventDispatcher::class))->method('subscribe', [
            DI::value($this->getEventType($rm)),
            DI::value($rc->getName()),
            DI::value($rm->getName()),
            DI::value($this->priority),
        ]);
    }

    private function getEventType(ReflectionMethod $rm): string
    {
        $params = $rm->getParameters();
        if (count($params) === 0) {
            throw new LogicException('Event listener must have at least one parameter');
        }

        $param = $params[0];
        $type = $param->getType();
        if (!$type instanceof ReflectionNamedType) {
            throw new LogicException('Event listener must have first parameter with type hint of event');
        }

        return $type->getName();
    }
}