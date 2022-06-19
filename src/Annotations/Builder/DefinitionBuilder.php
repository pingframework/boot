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

namespace Pingframework\Boot\Annotations\Builder;

use Pingframework\Boot\Annotations\AttributeScanner\AttributeScanner;
use Pingframework\Boot\Annotations\ServiceDefinitionRegistrar;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use ReflectionAttribute;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class DefinitionBuilder
{
    public function __construct(
        private AttributeScanner $scanner
    ) {}

    public function build(DependencyContainerInterface $c, array $namespaces, ?string $excludeRegexp = null): void
    {
        $rs = $this->scanner->scan($namespaces, $excludeRegexp);
        foreach ($rs->get(ServiceDefinitionRegistrar::class, true) as $rc) {
            /** @var ServiceDefinitionRegistrar $ai */
            $ai = $rc->getAttributes(
                ServiceDefinitionRegistrar::class,
                ReflectionAttribute::IS_INSTANCEOF
            )[0]->newInstance();
            $ai->registerService($rs, $c, $rc);
        }
    }
}