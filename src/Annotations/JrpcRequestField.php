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
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Http\Middleware\JrpcRequestMethodContext;
use Pingframework\Boot\Utils\ObjectMapper\DefaultObjectMapper;
use Pingframework\Boot\Utils\Strings\Strings;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class JrpcRequestField implements RuntimeArgumentInjector
{
    public function inject(
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionMethod             $rm,
        ReflectionParameter          $rp,
        array                        $runtime
    ): mixed {
        $params = $runtime[JrpcRequestMethodContext::class]->requestRootSchema->params;

        $paramName = Strings::camelCaseToUnderscore($rp->getName());
        $value = $params[$this->name ?? $paramName] ?? null;

        if ($value === null) {
            if (!$rp->isOptional()) {
                throw new RuntimeException(
                    sprintf(
                        'Required json rpc request param "%s" is not exists',
                        $this->name ?? $paramName
                    )
                );
            }

            $value = $rp->getDefaultValue();
        }

        return $value;
    }
}