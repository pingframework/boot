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

namespace Pingframework\Boot\Application;

use Pingframework\Boot\Annotations\Inject;
use Pingframework\Boot\Annotations\PingBootApplication;
use Pingframework\Boot\Http\Middleware\MiddlewareRegistry;
use Pingframework\Boot\Http\Routing\RouteRegistry;
use Pingframework\Boot\Http\Routing\SlimRequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[PingBootApplication]
class SlimPingBootApplication extends AbstractPingBootApplication
{
    public const CONFIG_SLIM_DISPLAY_ERRORS_DETAILS = 'slim.display_errors_details';
    public const CONFIG_SLIM_LOG_ERRORS             = 'slim.log_errors';
    public const CONFIG_SLIM_LOG_ERRORS_DETAILS     = 'slim.log_errors_details';

    #[Inject] protected RouteRegistry               $routeRegistry;
    #[Inject] protected MiddlewareRegistry          $middlewareRegistry;
    #[Inject] protected LoggerInterface             $logger;
    #[Inject] protected ?ErrorHandlerInterface      $errorHandler = null;
    #[Inject] protected SlimRequestHandlerInterface $requestHandler;

    #[Inject(self::CONFIG_SLIM_DISPLAY_ERRORS_DETAILS)]
    protected bool $displayErrorDetails = false;
    #[Inject(self::CONFIG_SLIM_LOG_ERRORS)]
    protected bool $logErrors           = false;
    #[Inject(self::CONFIG_SLIM_LOG_ERRORS_DETAILS)]
    protected bool $logErrorDetails     = false;

    private App $slimApp;

    public function configure(): void
    {
        $this->slimApp = AppFactory::create();

        /**
         * The routing middleware should be added earlier than the ErrorMiddleware
         * Otherwise exceptions thrown from it will not be handled by the middleware
         */
        $this->slimApp->addRoutingMiddleware();

        foreach ($this->routeRegistry->getUngroupedRoutes() as $routeDefinition) {
            $route = $this->slimApp->map(
                $routeDefinition->httpMethods,
                $routeDefinition->pattern,
                function (Request $request, Response $response, array $args) use ($routeDefinition) {
                    return $this->requestHandler->handle($routeDefinition, $request, $response, $args);
                }
            );

            foreach ($this->middlewareRegistry->getAll() as $mw) {
                if (!in_array($mw::class, $routeDefinition->ignoreMiddlewares)) {
                    $route->add($mw);
                }
            }
        }

        foreach ($this->routeRegistry->getGroupedRoutes() as $groupPattern => $routeDefinitions) {
            foreach ($routeDefinitions as $routeDefinition) {
                $this->slimApp->group($groupPattern, function (RouteCollectorProxy $group) use ($routeDefinition) {
                    $route = $group->map(
                        $routeDefinition->httpMethods,
                        $routeDefinition->pattern,
                        function (Request $request, Response $response, array $args) use ($routeDefinition) {
                            return $this->requestHandler->handle($routeDefinition, $request, $response, $args);
                        }
                    );

                    foreach ($this->middlewareRegistry->getAll() as $mw) {
                        if (!in_array($mw::class, $routeDefinition->ignoreMiddlewares)) {
                            $route->add($mw);
                        }
                    }
                });
            }
        }

        /**
         * Add the Slim body parsing middleware to the app middleware stack
         */
        $this->slimApp->addBodyParsingMiddleware();

        /**
         * Add Error Middleware
         *
         * @param bool                 $displayErrorDetails -> Should be set to false in production
         * @param bool                 $logErrors           -> Parameter is passed to the default ErrorHandler
         * @param bool                 $logErrorDetails     -> Display error details in error log
         * @param LoggerInterface|null $logger              -> Optional PSR-3 Logger
         *
         * Note: This middleware should be added last. It will not handle any exceptions/errors
         * for middleware added after it.
         */
        $errorMiddleware = $this->slimApp->addErrorMiddleware(
            $this->displayErrorDetails,
            $this->logErrors,
            $this->logErrorDetails,
            $this->logger
        );

        if ($this->errorHandler !== null) {
            $errorMiddleware->setDefaultErrorHandler($this->errorHandler);
        }
    }

    public function getSlimApp(): App
    {
        return $this->slimApp;
    }

    public function run(?ServerRequestInterface $request = null): static
    {
        $this->configure();
        $this->getSlimApp()->run($request);
        return $this;
    }
}