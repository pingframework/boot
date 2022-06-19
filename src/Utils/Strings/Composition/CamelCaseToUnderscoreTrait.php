<?php

namespace Pingframework\Boot\Utils\Strings\Composition;

trait CamelCaseToUnderscoreTrait
{
    public static function camelCaseToUnderscore(string $str): string
    {
        return ltrim(
            strtolower(
                preg_replace(
                    '/[A-Z]([A-Z](?![a-z]))*/',
                    '_$0',
                    $str
                )
            ),
            '_'
        );
    }
}