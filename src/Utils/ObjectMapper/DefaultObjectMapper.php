<?php

/**
 * Ping Boot
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phpsuit.net so we can send you a copy immediately.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Boot\Utils\ObjectMapper;


use Pingframework\Boot\Utils\Arrays\ArrayGetterHelper;
use Pingframework\Boot\Utils\Json\JsonDecoder;
use Pingframework\Boot\Utils\Json\JsonEncoder;
use JetBrains\PhpStorm\Pure;

/**
 * Facade for ObjectMappers.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class DefaultObjectMapper extends ObjectMapper
{
    #[Pure] public function __construct()
    {
        $arrayGetterHelper = new ArrayGetterHelper();
        $arrayObjectEncoder = new ArrayObjectEncoder();
        $arrayObjectDecoder = new ArrayObjectDecoder($arrayGetterHelper);
        $jsonEncoder = new JsonEncoder();

        parent::__construct(
            new ArrayObjectMapper(
                $arrayObjectEncoder,
                $arrayObjectDecoder
            ),
            new JsonObjectMapper(
                new JsonObjectEncoder(
                    $jsonEncoder,
                    $arrayObjectEncoder
                ),
                new JsonObjectDecoder(
                    $arrayObjectDecoder,
                    new JsonDecoder()
                )
            )
        );
    }
}