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

use Pingframework\Boot\Annotations\JrpcController;
use Pingframework\Boot\Annotations\JrpcRequestField;
use Pingframework\Boot\Annotations\JrpcRequestSchema;
use Pingframework\Boot\Annotations\Service;
use Pingframework\Boot\Http\Middleware\JrpcRequestMethodContext;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service]
class JrpcTestController
{
    #[JrpcController('test.test')]
    public function testMethod(
        JrpcRequestMethodContext  $ctx,
        #[JrpcRequestField]
        string                    $userId,
        #[JrpcRequestSchema]
        TestJrpcRequestJsonSchema $requestSchema,
    ): TestJrpcRequestJsonSchema {
        return $requestSchema;
    }
}