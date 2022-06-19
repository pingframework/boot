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
use Pingframework\Boot\Command\AutowireConfigurable;
use Pingframework\Boot\Command\CommandsRegistry;
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
class Command extends Variadic
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $description = null,
        public readonly array   $aliases = [],
        public readonly bool    $hidden = false,
    ) {
        parent::__construct(
            targetServices: [CommandsRegistry::class],
        );
    }

    public function registerService(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc
    ): void {
        if (!$rc->isSubclassOf(AutowireConfigurable::class)) {
            throw new DependencyContainerException(
                sprintf(
                    'Class %s must implement interface %s',
                    $rc->getName(),
                    AutowireConfigurable::class
                )
            );
        }

        parent::registerService($rs, $c, $rc);

        $this->findAutowire($c, $rc)->method('autowireConfigure', [
            'name'        => DI::value($this->name),
            'description' => DI::value($this->description),
            'aliases'     => DI::value($this->aliases),
            'hidden'      => DI::value($this->hidden),
        ]);
    }
}