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

namespace Pingframework\Boot\Http\Routing;

use Pingframework\Boot\Annotations\RuntimeArgumentInjector;
use Pingframework\Boot\Annotations\Service;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\DependencyContainer\ServiceNotFoundException;
use Pingframework\Boot\DependencyContainer\ServiceResolveException;
use Pingframework\Boot\Utils\ObjectMapper\ObjectMapperException;
use Pingframework\Boot\Utils\ObjectMapper\ObjectMapperInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(SlimRequestHandlerInterface::class)]
class SlimRequestHandler implements SlimRequestHandlerInterface
{
    public function __construct(
        public readonly DependencyContainerInterface $c,
        public readonly ObjectMapperInterface        $om,
    ) {}

    /**
     * @param RouteDefinition $rd
     * @param Request         $request
     * @param Response        $response
     * @param array           $args
     * @return ResponseInterface
     * @throws DependencyContainerException
     * @throws ServiceNotFoundException
     * @throws ServiceResolveException
     * @throws ReflectionException
     * @throws ObjectMapperException
     */
    public function handle(RouteDefinition $rd, Request $request, Response $response, array $args): ResponseInterface
    {
        foreach ($args as $k => $v) {
            $request = $request->withAttribute($k, $v);
        }

        $controller = $this->c->get($rd->className);
        $rc = new ReflectionClass($rd->className);
        $rm = $rc->getMethod($rd->methodName);

        return $this->processResponse(
            $response,
            $rm->invoke($controller, ...$this->resolveArgs($rd, $rc, $rm, $request, $response))
        );
    }

    /**
     * @param ResponseInterface $response
     * @param mixed             $result
     * @return ResponseInterface
     * @throws ObjectMapperException
     */
    protected function processResponse(ResponseInterface $response, mixed $result): ResponseInterface
    {
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        if (is_object($result) || is_array($result)) {
            $response->getBody()->write(
                is_object($result)
                    ? $this->om->unmapToJson($result)
                    : $this->om->unmapListToJson($result)
            );
            $response = $response->withHeader('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     * @param RouteDefinition  $rd
     * @param ReflectionClass  $rc
     * @param ReflectionMethod $rm
     * @param Request          $request
     * @param Response         $response
     * @return array
     * @throws DependencyContainerException
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     * @throws ServiceResolveException
     */
    protected function resolveArgs(
        RouteDefinition  $rd,
        ReflectionClass  $rc,
        ReflectionMethod $rm,
        Request          $request,
        Response         $response,
    ): array {
        $args = [];
        $runtime = [
            ServerRequestInterface::class => $request,
            ResponseInterface::class      => $response,
            RouteDefinition::class        => $rd,
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

                if ($trc->isSubclassOf(ServerRequestInterface::class) || $type->getName(
                    ) === ServerRequestInterface::class) {
                    $args[$rp->getName()] = $request;
                    continue;
                }

                if ($trc->isSubclassOf(ResponseInterface::class) || $type->getName() === ResponseInterface::class) {
                    $args[$rp->getName()] = $response;
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