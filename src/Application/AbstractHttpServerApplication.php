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

use Pingframework\Boot\Http\Server\HttpRequestHandlerRegistry;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
abstract class AbstractHttpServerApplication extends AbstractPingBootApplication
{
    public const CONFIG_SERVER           = 'http_server';
    public const CONFIG_SERVER_BIND_HOST = 'bind_host';
    public const CONFIG_SERVER_BIND_PORT = 'bind_port';
    public const DEFAULT_HOST            = '127.0.0.1';
    public const DEFAULT_PORT            = 8080;

    protected Server $server;

    public function __construct(
        DependencyContainerInterface               $applicationContext,
        public readonly HttpRequestHandlerRegistry $handlerRegistry,
    ) {
        parent::__construct($applicationContext);
    }

    protected function configure(?string $host = null, ?int $port = null): void
    {
        $config = $this->getConfig($host, $port);

        $this->server = new Server(
            $config[self::CONFIG_SERVER_BIND_HOST],
            $config[self::CONFIG_SERVER_BIND_PORT],
            SWOOLE_PROCESS,
            SWOOLE_SOCK_TCP
        );

        unset($config[self::CONFIG_SERVER_BIND_HOST]);
        unset($config[self::CONFIG_SERVER_BIND_PORT]);
        $this->server->set($config);
    }

    protected function getConfig(?string $host = null, ?int $port = null): array
    {
        $config = [
            self::CONFIG_SERVER_BIND_HOST => self::DEFAULT_HOST,
            self::CONFIG_SERVER_BIND_PORT => self::DEFAULT_PORT,
        ];

        if ($this->getApplicationContext()->has(self::CONFIG_SERVER)) {
            return array_merge($config, $this->getApplicationContext()->get(self::CONFIG_SERVER));
        }

        if (!is_null($host)) {
            $config[self::CONFIG_SERVER_BIND_HOST] = $host;
        }

        if (!is_null($port)) {
            $config[self::CONFIG_SERVER_BIND_PORT] = $port;
        }

        return $config;
    }

    public function listen(?string $host = null, ?int $port = null): void
    {
        $this->configure($host, $port);

        $this->server->on('request', function (Request $request, Response $response) {
            foreach ($this->handlerRegistry->handlers as $handler) {
                $handler->handle($request, $response);
            }

            $response->end();
        });

        $this->server->start();
    }
}