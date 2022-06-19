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
use http\Exception\RuntimeException;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Slim\Exception\HttpBadRequestException;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class RequestAttribute implements RuntimeArgumentInjector
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
        $value = $runtime[ServerRequestInterface::class]->getAttribute($this->name ?? $rp->getName());

        if ($value === null) {
            if (!$rp->isOptional()) {
                throw new HttpBadRequestException(
                    $runtime[ServerRequestInterface::class],
                    sprintf(
                        'Required attribute "%s" is not present in request',
                        $this->name ?? $rp->getName()
                    )
                );
            }

            $value = $rp->getDefaultValue();
        }

        return $value;
    }
}