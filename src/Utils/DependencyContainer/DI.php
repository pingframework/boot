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

namespace Pingframework\Boot\Utils\DependencyContainer;

use Closure;
use JetBrains\PhpStorm\Pure;
use Pingframework\Boot\DependencyContainer\Definition\AutowireDefinition;
use Pingframework\Boot\DependencyContainer\Definition\CallbackDefinition;
use Pingframework\Boot\DependencyContainer\Definition\InjectDefinition;
use Pingframework\Boot\DependencyContainer\Definition\ValueDefinition;
use Pingframework\Boot\DependencyContainer\Definition\VariadicDefinition;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
abstract class DI
{
    #[Pure] public static function callback(Closure $closure): CallbackDefinition
    {
        return new CallbackDefinition($closure);
    }

    #[Pure] public static function func(Closure $closure): CallbackDefinition
    {
        return self::callback($closure);
    }

    #[Pure] public static function closure(Closure $closure): CallbackDefinition
    {
        return self::callback($closure);
    }

    #[Pure] public static function value(mixed $value): ValueDefinition
    {
        return new ValueDefinition($value);
    }

    #[Pure] public static function inject(string $containerKey, bool $isOptional = false, mixed $default = null): InjectDefinition
    {
        return new InjectDefinition($containerKey, $isOptional, $default);
    }

    #[Pure] public static function get(string $containerKey, bool $isOptional = false, mixed $default = null): InjectDefinition
    {
        return self::inject($containerKey, $isOptional, $default);
    }

    #[Pure] public static function variadic(string ...$service): VariadicDefinition
    {
        return new VariadicDefinition(...$service);
    }

    #[Pure] public static function autowire(string $className): AutowireDefinition
    {
        return new AutowireDefinition($className);
    }
}