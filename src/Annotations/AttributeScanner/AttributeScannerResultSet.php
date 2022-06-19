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

namespace Pingframework\Boot\Annotations\AttributeScanner;

use Attribute;
use Pingframework\Boot\Annotations\Variadic;
use ReflectionClass;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class AttributeScannerResultSet
{
    /**
     * @var array<class-string, array<ReflectionClass>>
     */
    private array $attributes = [];

    /**
     * @var array<class-string, ReflectionClass>
     */
    private array $classes = [];

    /**
     * @var array<class-string, array<array<class-string, int>>>
     */
    private array $variadics = [];

    public function add(ReflectionClass $rc): void
    {
        $this->classes[$rc->getName()] = $rc;

        foreach ($rc->getAttributes() as $attribute) {
            if ($attribute->getName() === Attribute::class) {
                continue;
            }
            $this->attributes[$attribute->getName()][] = $rc;

            if (is_subclass_of($attribute->getName(), Variadic::class) || $attribute->getName() === Variadic::class) {
                /** @var Variadic $ai */
                $ai = $attribute->newInstance();
                foreach ($ai->targetServices as $targetService) {
                    $this->variadics[$targetService][] = [$rc->getName(), $ai->priority];
                }
            }
        }
    }

    /**
     * @return array<class-string>
     */
    public function getVariadics(string $targetService): array
    {
        $list = $this->variadics[$targetService] ?? [];
        // sort by priority
        usort($list, fn(array $a, array $b): int => $b[1] <=> $a[1]);
        return array_column($list, 0);
    }

    public function dropVariadic(string $targetService, string $serviceToDrop): void
    {
        $l = [];
        foreach ($this->variadics[$targetService] ?? [] as $v) {
            if ($v[0] !== $serviceToDrop) {
                $l[] = $v;
            }
        }

        $this->variadics[$targetService] = $l;
    }

    /**
     * Returns list of classes for given attribute.
     *
     * @param class-string $attribute
     *
     * @return ReflectionClass[]
     */
    public function get(string $attribute, bool $isInstanceOf = false): array
    {
        if ($isInstanceOf) {
            $l = [];

            foreach ($this->getAll() as $ac => $rcList) {
                if (is_subclass_of($ac, $attribute) || $ac === $attribute) {
                    foreach ($rcList as $rc) {
                        $l[$rc->getName()] = $rc;
                    }
                }
            }

            return array_values($l);
        }

        return $this->attributes[$attribute] ?? [];
    }

    /**
     * @return array<class-string, array<ReflectionClass>>
     */
    public function getAll(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<class-string, ReflectionClass>
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}
