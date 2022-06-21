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

namespace Pingframework\Boot\Http\Routing\Jrpc;

use Pingframework\Boot\Annotations\RuntimeArgumentInjector;
use Pingframework\Boot\Annotations\Service;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Http\Middleware\JrpcRequestMethodContext;
use Pingframework\Boot\Utils\ObjectMapper\ObjectMapper;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(JrpcRequestHandlerInterface::class)]
class JrpcRequestHandler implements JrpcRequestHandlerInterface
{
    public function __construct(
        public readonly DependencyContainerInterface $c,
        public readonly ObjectMapper                 $objectMapper
    ) {}

    public function handle(JrpcRouteDefinition $rd, JrpcRequestMethodContext $ctx): void
    {
        $controller = $this->c->get($rd->className);
        $rc = new ReflectionClass($rd->className);
        $rm = $rc->getMethod($rd->methodName);

        $this->processResult(
            $ctx,
            $rm->invoke($controller, ...$this->getArgs($rd, $rc, $rm, $ctx))
        );
    }

    private function processResult(JrpcRequestMethodContext $ctx, mixed $result): void
    {
        if (is_object($result)) {
            $ctx->responseRootSchema->result = $this->objectMapper->unmapToArray($result);
            return;
        }

        if ($this->isUnmappableList($result)) {
            $ctx->responseRootSchema->result = $this->objectMapper->unmapListToArray($result);
            return;
        }

        $ctx->responseRootSchema->result = $result;
    }

    private function isUnmappableList(mixed $result): bool
    {
        if (!is_array($result)) {
            return false;
        }

        if (count($result) === 0) {
            return false;
        }

        if (is_object(reset($result))) {
            return true;
        }

        return false;
    }

    private function getArgs(
        JrpcRouteDefinition      $rd,
        ReflectionClass          $rc,
        ReflectionMethod         $rm,
        JrpcRequestMethodContext $ctx,
    ): array {
        $args = [];
        $runtime = [
            JrpcRequestMethodContext::class => $ctx,
            JrpcRouteDefinition::class      => $rd,
        ];

        foreach ($rm->getParameters() as $rp) {
            $rai = $this->getArgumentInjector($rp);
            if ($rai !== null) {
                $args[$rp->getName()] = $rai->inject($this->c, $rc, $rm, $rp, $runtime);
                continue;
            }

            $type = $rp->getType();

            if ($type->isBuiltin()) {
                throw new DependencyContainerException(
                    sprintf(
                        'Could not resolve argument %s of method %s::%s',
                        $rp->getName(),
                        $rc->getName(),
                        $rm->getName()
                    )
                );
            }

            if ($type instanceof ReflectionNamedType) {
                $trc = new ReflectionClass($type->getName());

                if ($trc->isSubclassOf(JrpcRequestMethodContext::class) || $type->getName(
                    ) === JrpcRequestMethodContext::class) {
                    $args[$rp->getName()] = $ctx;
                    continue;
                }

                $args[$rp->getName()] = $this->c->get($type->getName());
                continue;
            }

            throw new DependencyContainerException(
                sprintf(
                    'Could not resolve argument %s of method %s::%s',
                    $rp->getName(),
                    $rc->getName(),
                    $rm->getName()
                )
            );
        }

        return $args;
    }

    protected function getArgumentInjector(ReflectionParameter $rp): ?RuntimeArgumentInjector
    {
        foreach (
            $rp->getAttributes(
                RuntimeArgumentInjector::class,
                ReflectionAttribute::IS_INSTANCEOF
            ) as $attribute
        ) {
            return $attribute->newInstance();
        }

        return null;
    }
}