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
use Pingframework\Boot\Utils\Strings\Strings;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class RequestBodyField implements RuntimeArgumentInjector
{
    public function __construct(
        public readonly ?string $name = null,
    ) {}

    public function inject(
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionMethod             $rm,
        ReflectionParameter          $rp,
        array                        $runtime
    ): mixed {
        /** @var Request $request */
        $request = $runtime[ServerRequestInterface::class];
        $paramName = Strings::camelCaseToUnderscore($rp->getName());
        $value = $request->getParsedBody()[$this->name ?? $paramName] ?? null;

        if ($value === null) {
            if (!$rp->isOptional()) {
                throw new HttpBadRequestException(
                    $runtime[ServerRequestInterface::class],
                    sprintf(
                        'Required POST param "%s" is not present in request',
                        $this->name ?? $paramName
                    )
                );
            }

            $value = $rp->getDefaultValue();
        }

        return $value;
    }
}