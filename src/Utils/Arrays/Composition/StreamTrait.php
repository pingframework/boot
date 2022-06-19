<?php

namespace Pingframework\Boot\Utils\Arrays\Composition;

use FluentTraversable\FluentTraversable;

trait StreamTrait
{
    /**
     * Returns a new FluentTraversable instance.
     *
     * @param array $array
     * @return FluentTraversable
     */
    public static function stream(array $array): FluentTraversable
    {
        return FluentTraversable::from($array);
    }
}