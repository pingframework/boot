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

namespace Pingframework\Boot\Annotations;


use Attribute;
use Pingframework\Boot\Utils\ObjectMapper\Decoder\DefaultValueDecoder;
use Pingframework\Boot\Utils\ObjectMapper\Encoder\DefaultValueEncoder;

/**
 * Marks property/parameter as a serializable for ObjectMapper.
 *
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class MapProperty
{
    public const TYPE_INT    = "int";
    public const TYPE_STRING = "string";
    public const TYPE_BOOL   = "bool";
    public const TYPE_FLOAT  = "float";
    public const TYPE_ARRAY  = "array";
    public const TYPE_MIXED  = "mixed";

    public function __construct(
        private ?string $key = null,
        private bool    $omitempty = false,
        private string  $entityType = self::TYPE_MIXED,
        private string  $decoder = DefaultValueDecoder::class,
        private string  $encoder = DefaultValueEncoder::class,
    ) {}

    /**
     * @return string
     */
    public function getDecoder(): string
    {
        return $this->decoder;
    }

    /**
     * @return string
     */
    public function getEncoder(): string
    {
        return $this->encoder;
    }

    /**
     * @return string
     */
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function isOmitempty(): bool
    {
        return $this->omitempty;
    }
}