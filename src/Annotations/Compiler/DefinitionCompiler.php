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

namespace Pingframework\Boot\Annotations\Compiler;

use Brick\VarExporter\ExportException;
use Brick\VarExporter\VarExporter;
use CompileError;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class DefinitionCompiler
{
    /**
     * Takes definitions from the container and compiles the PHP code for them.
     * Saves the compiled code to the cache file if file is not null.
     * Creates directories recursively if they do not exist.
     *
     * @param DependencyContainerInterface $c
     * @param string|null                  $file
     * @param int                          $permissions
     * @return string
     * @throws ExportException
     */
    public function compile(DependencyContainerInterface $c, ?string $file = null, int $permissions = 0777): string
    {
        $content = implode(PHP_EOL, [
            '<?php',
            '',
            '// This file is automatically generated by DefinitionCompiler. DO NOT MODIFY!',
            '',
            VarExporter::export(
                $c->getDefinitions(),
                VarExporter::ADD_RETURN |
                VarExporter::CLOSURE_SNAPSHOT_USES |
                VarExporter::INLINE_NUMERIC_SCALAR_ARRAY
            ),
        ]);

        if ($file !== null) {
            $dir = dirname($file);

            if (!is_dir($dir)) {
                mkdir($dir, $permissions, true);
            }

            if (file_put_contents($file, $content) === false) {
                throw new CompileError('Failed to write compiled definitions to file ' . $file);
            }
        }

        return $content;
    }
}