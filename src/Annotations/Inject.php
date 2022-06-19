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
use JetBrains\PhpStorm\Pure;
use Pingframework\Boot\Annotations\AttributeScanner\AttributeScannerResultSet;
use Pingframework\Boot\Annotations\Composition\PropertyDefinitionRegistrarTrait;
use Pingframework\Boot\DependencyContainer\Definition\DefinitionInterface;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Inject implements ValueDefinitionBuilder, PropertyDefinitionRegistrar, RuntimeArgumentInjector
{
    use PropertyDefinitionRegistrarTrait;

    public function __construct(
        public readonly ?string $service = null,
    ) {}

    #[Pure] public function buildDefinition(
        AttributeScannerResultSet              $rs,
        DependencyContainerInterface           $c,
        ReflectionClass                        $rc,
        ReflectionParameter|ReflectionProperty $rp,
        ?ReflectionMethod                      $rm = null,
    ): DefinitionInterface {
        $service = $this->service;

        if ($service === null) {
            $type = $rp->getType();
            $service = $type->isBuiltin() ? $rp->getName() : $type->getName();
        }

        $isOptional = $rp instanceof ReflectionParameter ? $rp->isOptional() : $rp->isDefault();
        $default = $isOptional ? $rp->getDefaultValue() : null;

        return DI::get($service, $isOptional, $default);
    }

    public function inject(
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionMethod             $rm,
        ReflectionParameter          $rp,
        array                        $runtime
    ): mixed {
        return $this->buildDefinition(new AttributeScannerResultSet(), $c, $rc, $rp, $rm)->resolve($c);
    }
}