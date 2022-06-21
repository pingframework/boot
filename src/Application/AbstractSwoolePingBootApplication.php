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

use Ilex\SwoolePsr7\SwooleResponseConverter;
use Ilex\SwoolePsr7\SwooleServerRequestConverter;
use Pingframework\Boot\Annotations\ConfigFile;
use Pingframework\Boot\Annotations\Inject;
use Pingframework\Boot\Annotations\PingBootApplication;
use Pingframework\Boot\Http\Server\SwooleHttpRequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Factory\Psr17\SlimPsr17Factory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UploadedFileFactory;
use Slim\Psr7\Factory\UriFactory;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
abstract class AbstractSwoolePingBootApplication extends AbstractPingBootApplication
{
    public const CONFIG_SWOOLE           = 'swoole';
    public const CONFIG_SWOOLE_BIND_HOST = 'bind_host';
    public const CONFIG_SWOOLE_BIND_PORT = 'bind_port';
    public const DEFAULT_HOST            = '127.0.0.1';
    public const DEFAULT_PORT            = 8080;

    #[Inject(self::CONFIG_SWOOLE)]
    protected array $config = [];

    #[Inject]
    protected LoggerInterface $logger;

    protected Server $swooleServer;

    abstract public function getHandler(): SwooleHttpRequestHandler;

    public function configure(?string $host = null, ?int $port = null): void
    {
        if ($host !== null) {
            $this->config[self::CONFIG_SWOOLE_BIND_HOST] = $host;
        }
        if ($port !== null) {
            $this->config[self::CONFIG_SWOOLE_BIND_PORT] = $port;
        }

        $host = $this->config[self::CONFIG_SWOOLE_BIND_HOST] ?? self::DEFAULT_HOST;
        $port = $this->config[self::CONFIG_SWOOLE_BIND_PORT] ?? self::DEFAULT_PORT;

        $this->swooleServer = new Server($host, $port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
    }

    public function listen(?string $host = null, ?int $port = null): void
    {
        $handler = $this->getHandler();

        $handler->configure();
        $this->configure($host, $port);

        $this->swooleServer->on('request', function (Request $request, Response $response) use ($handler) {
            $handler->handle($request, $response);
        });

        $this->swooleServer->start();
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}