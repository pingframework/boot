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

namespace Pingframework\Boot\Utils\ObjectMapper\Decoder;


use Pingframework\Boot\Utils\Arrays\ArrayGetterHelperInterface;
use Pingframework\Boot\Utils\ObjectMapper\ArrayObjectDecoderInterface;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Base class for value decoders.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
abstract class AbstractValueDecoder implements ValueDecoderInterface
{
    public function __construct(
        protected ArrayObjectDecoderInterface $objectDecoder,
        protected ArrayGetterHelperInterface  $arrayGetterHelper,
    ) {}

    protected function hasDefaultValue(ReflectionParameter|ReflectionProperty $rp): bool
    {
        return $rp instanceof ReflectionParameter ? $rp->isOptional() : $rp->hasDefaultValue();
    }

    protected function getDefaultValue(ReflectionParameter|ReflectionProperty $rp): mixed
    {
        return $this->hasDefaultValue($rp)
            ? $rp->getDefaultValue() // exception could be never thrown if it has default value
            : null;
    }
}