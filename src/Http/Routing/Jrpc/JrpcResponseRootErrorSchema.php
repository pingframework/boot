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

use Pingframework\Boot\Annotations\JsonProperty;
use Throwable;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class JrpcResponseRootErrorSchema
{
    #[JsonProperty]
    public int $code = -32603;
    #[JsonProperty]
    public string $message = 'Internal server error';
    #[JsonProperty(omitempty: true)]
    public array $data = [];

    public static function fromException(Throwable $e): self
    {
        $error = new static();
        $error->code = $e->getCode() <= -32000 ? $e->getCode() : -32603;
        $error->message = $e->getMessage();
        return $error;
    }
}