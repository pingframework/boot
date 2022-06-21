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

use Pingframework\Boot\Annotations\ConfigFile;
use Pingframework\Boot\Annotations\PingBootApplication;
use Pingframework\Boot\Http\Server\JrpcSwooleHttpRequestHandler;
use Pingframework\Boot\Http\Server\SwooleHttpRequestHandler;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[PingBootApplication]
#[ConfigFile(__DIR__ . '/../../etc/swoole.php', __DIR__ . '/../../etc/jrpc.php')]
class JrpcSwoolePingBootApplication extends AbstractSwoolePingBootApplication
{
    public function __construct(
        private readonly JrpcSwooleHttpRequestHandler $handler,
    ) {}

    public function getHandler(): SwooleHttpRequestHandler
    {
        return $this->handler;
    }
}