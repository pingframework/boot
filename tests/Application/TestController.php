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

namespace Pingframework\Boot\Tests\Application;

use Pingframework\Boot\Annotations\Controller;
use Pingframework\Boot\Annotations\ControllerGroup;
use Pingframework\Boot\Annotations\Header;
use Pingframework\Boot\Annotations\Inject;
use Pingframework\Boot\Annotations\RequestAttribute;
use Pingframework\Boot\Annotations\RequestJsonField;
use Pingframework\Boot\Annotations\RequestSchema;
use Pingframework\Boot\Annotations\RequestBodyField;
use Pingframework\Boot\Annotations\RequestQueryParam;
use Pingframework\Boot\Annotations\ResponseSchema;
use Pingframework\Boot\Annotations\UploadedFile;
use Pingframework\Boot\Http\Routing\RouteRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[ControllerGroup('/group')]
class TestController
{
    #[Controller('/test/{id1}')]
    public function testAction1(
        #[Inject]
        string $foo,
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRegistry $routeRegistry,
        #[RequestAttribute]
        int $id1,
        #[RequestQueryParam]
        int $id2,
    ): void {
        $response->getBody()->write('TEST');
    }

    #[Controller('/test2', ['POST'])]
    public function testAction2(
        ResponseInterface $response,
        #[Header('X-Foo')]
        string $header,
        #[RequestBodyField]
        int $id1,
        #[UploadedFile('field1')]
        \Slim\Psr7\UploadedFile $file,
    ): void {
        $response->getBody()->write((string)$id1);
    }

    #[Controller('/test3', ['POST'])]
    public function testAction3(
        #[RequestSchema]
        TestRequestJsonSchema $requestSchema,
        #[ResponseSchema]
        TestResponseJsonSchema $responseSchema,
        #[RequestBodyField]
        int $id1,
    ): TestResponseJsonSchema {
        $responseSchema->id1 = $requestSchema->id1;
        return $responseSchema;
    }
}