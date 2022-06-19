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

namespace Pingframework\Boot\Utils\ObjectMapper\Encoder;


use Pingframework\Boot\Annotations\MapProperty;
use Pingframework\Boot\Utils\ObjectMapper\ObjectMapperException;
use ReflectionObject;
use ReflectionProperty;

/**
 * Responsible for converting object property to array entity.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class DefaultValueEncoder extends AbstractValueEncoder
{
    /**
     * Converts object property to array entity.
     *
     * @param MapProperty $mp
     * @param ReflectionProperty $rp
     * @param ReflectionObject $ro
     * @param mixed $value
     * @return mixed
     * @throws ObjectMapperException
     */
    public function marshal(
        MapProperty        $mp,
        ReflectionProperty $rp,
        ReflectionObject   $ro,
        mixed              $value,
    ): mixed {
        if (is_array($value)) {
            $res = [];

            foreach ($value as $key => $entity) {
                $res[$key] = $this->marshal($mp, $rp, $ro, $entity);
            }

            return $res;
        }

        if (is_object($value)) {
            return $this->objectEncoder->marshal($value);
        }

        return $value;
    }
}