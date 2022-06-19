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

namespace Pingframework\Boot\Utils\Json;


use Pingframework\Boot\Annotations\Service;
use JsonException;

/**
 * JSON decoder.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(JsonDecoderInterface::class)]
class JsonDecoder implements JsonDecoderInterface
{
    /**
     * Converts body json string into PHP array.
     *
     * @param string $jsonString
     *
     * @return array
     *
     * @throws JsonException in case when can't decode json string.
     */
    public function unmarshal(string $jsonString): array
    {
        if (strlen($jsonString) == 0) {
            return [];
        }

        return json_decode(
            $jsonString,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
