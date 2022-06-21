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

namespace Pingframework\Boot\Http\Server;

use Ilex\SwoolePsr7\SwooleResponseConverter;
use Ilex\SwoolePsr7\SwooleServerRequestConverter;
use Pingframework\Boot\Annotations\Service;
use Pingframework\Boot\Application\SlimPingBootApplication;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UploadedFileFactory;
use Slim\Psr7\Factory\UriFactory;
use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service]
class SlimSwooleHttpRequestHandler implements SwooleHttpRequestHandler
{
    private SwooleServerRequestConverter $requestConverter;

    public function __construct(
        public readonly SlimPingBootApplication $slimPba
    ) {}

    public function handle(Request $request, Response $response): void
    {
        // let slim handle request as regular PSR7 request
        $psr7Response = $this->slimPba->getSlimApp()->handle(
        // convert swoole request to PSR7/Slim request
            $this->requestConverter->createFromSwoole($request)
        );
        // response bridge PSR7/Slim to Swoole
        $converter = new SwooleResponseConverter($response);
        $converter->send($psr7Response);
    }

    public function configure(): void
    {
        $this->slimPba->configure();

        // request bridge Swoole to PSR7/Slim
        $this->requestConverter = new SwooleServerRequestConverter(
            new ServerRequestFactory(),
            new UriFactory(),
            new UploadedFileFactory(),
            new StreamFactory()
        );
    }
}