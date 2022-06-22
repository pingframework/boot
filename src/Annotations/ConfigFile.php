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
use Pingframework\Boot\Application\ConfigFileRegistry;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use ReflectionClass;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ConfigFile implements ServiceDefinitionRegistrar
{
    use MethodDefinitionRegistrarTrait;

    public function __construct(
        public readonly array $paths,
    ) {}

    /**
     * @throws DependencyContainerException
     */
    public function registerService(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
    ): void {
        $this->findAutowire($c, new ReflectionClass(ConfigFileRegistry::class))->method(
            'add',
            [
                DI::value($rc->getName()),
                DI::value($this->paths),
            ],
        );
    }
}