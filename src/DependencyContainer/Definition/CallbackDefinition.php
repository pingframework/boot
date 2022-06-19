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

namespace Pingframework\Boot\DependencyContainer\Definition;

use Closure;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class CallbackDefinition implements DefinitionInterface
{
    public function __construct(
        public readonly Closure $callback,
    ) {}

    public function resolve(DependencyContainerInterface $c): mixed
    {
        return $this->callback->call($c, $c);
    }

    public static function __set_state(array $array): DefinitionInterface
    {
        return new self($array['callback']);
    }
}