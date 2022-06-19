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
use Pingframework\Boot\Annotations\Composition\MethodDefinitionRegistrarTrait;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use ReflectionAttribute;
use ReflectionClass;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Service implements ServiceDefinitionRegistrar
{
    use MethodDefinitionRegistrarTrait;

    private array $aliases;

    public function __construct(
        string ...$aliases
    ) {
        $this->aliases = $aliases;
    }

    /**
     * Register definition in the dependency container.
     *
     * @param AttributeScannerResultSet    $rs
     * @param DependencyContainerInterface $c
     * @param ReflectionClass              $rc
     * @return void
     * @throws DependencyContainerException
     */
    public function registerService(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc
    ): void {
        $this->findAutowire($c, $rc);
        $constructor = $rc->getConstructor();
        if ($constructor) {
            $this->registerMethod($rs, $c, $rc, $constructor);
        }

        foreach ($rc->getProperties() as $rp) {
            if ($rp->isPromoted()) {
                continue;
            }

            foreach (
                $rp->getAttributes(
                    PropertyDefinitionRegistrar::class,
                    ReflectionAttribute::IS_INSTANCEOF
                ) as $attribute
            ) {
                /** @var PropertyDefinitionRegistrar $ai */
                $ai = $attribute->newInstance();
                $ai->registerProperty($rs, $c, $rc, $rp);
            }
        }

        foreach ($rc->getMethods() as $rm) {
            foreach (
                $rm->getAttributes(
                    MethodDefinitionRegistrar::class,
                    ReflectionAttribute::IS_INSTANCEOF
                ) as $attribute
            ) {
                /** @var MethodDefinitionRegistrar $ai */
                $ai = $attribute->newInstance();
                $ai->registerMethod($rs, $c, $rc, $rm);
            }
        }

        foreach ($this->aliases as $alias) {
            $c->set($alias, DI::get($rc->getName()));
        }
    }
}