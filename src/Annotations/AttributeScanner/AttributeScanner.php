<?php

/**
 * BBum Framework
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

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;
use SplFileInfo;
use Throwable;


/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class AttributeScanner
{
    /**
     * Scans a namespaces for classes.
     * Takes a directories for scanning from composer's autoload_psr4 namespace map
     * and filter it by given namespace list. So, to find an annotated classes the psr4 standard must be used.
     * It also will exclude classes by given exclude regexp pattern.
     *
     * @param array       $namespaces
     * @param string|null $excludeRegexp
     *
     * @return AttributeScannerResultSet
     *
     * @throws Exception
     */
    public function scan(array $namespaces, ?string $excludeRegexp = null): AttributeScannerResultSet
    {
        $resultSet = new AttributeScannerResultSet();

        foreach ($this->getDirs(...$namespaces) as $ns) {
            /** @var SplFileInfo $f */
            foreach ($this->getIterator($ns[1]) as $f) {
                if (!$f->isFile()) {
                    continue;
                }

                $rc = $this->getClass($ns, $f);
                if ($rc === null || !$this->testNamespace($rc, $namespaces, $excludeRegexp)) {
                    continue;
                }

                $resultSet->add($rc);
            }
        }

        return $resultSet;
    }

    private function testNamespace(ReflectionClass $rc, array $namespaces, ?string $excludeRegexp = null): bool
    {
        foreach ($namespaces as $ns) {
            if (str_starts_with($rc->getNamespaceName(), $ns)) {
                if ($excludeRegexp !== null && preg_match("/$excludeRegexp/", $rc->getName())) {
                    return false;
                }
                return true;
            }
        }

        return false;
    }

    /**
     * @throws Exception
     */
    private function getNamespaceMap(): array
    {
        $dir = __DIR__; // xdebug fix

        $cf = 'autoload_psr4.php';
        $dl = [
            $dir . '/../../../../../../vendor/composer/' . $cf,
            $dir . '/../../../vendor/composer/' . $cf,
        ];

        foreach ($dl as $fp) {
            if (file_exists($fp)) {
                return require $fp;
            }
        }

        throw new Exception('Unable to find composer autoloader');
    }

    /**
     * @param string $dir
     * @return RegexIterator
     */
    private function getIterator(string $dir): RegexIterator
    {
        return new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir)
            ),
            "/[A-Z]{1}\w*\.php$/"
        );
    }

    /**
     * @return array<array>
     * @throws Exception
     */
    private function getDirs(string ...$namespaces): array
    {
        $nsMap = $this->getNamespaceMap();
        $dirs = [];

        // critical for performance, try to decrease the number of folders to scan
        foreach ($namespaces as $targetNs) {
            foreach ($nsMap as $composerNs => $dir) {
                if (str_starts_with($composerNs, $targetNs) || str_starts_with($targetNs, $composerNs)) {
                    $targetDir = $dir[0];

                    if (str_starts_with($targetNs, $composerNs)) {
                        $targetDir = $targetDir . DIRECTORY_SEPARATOR . str_replace(
                                '\\',
                                DIRECTORY_SEPARATOR,
                                substr($targetNs, strlen($composerNs))
                            );
                    }

                    if (!is_dir($targetDir)) {
                        continue;
                    }

                    $dirs[] = [$composerNs, $targetDir, $dir[0]];
                }
            }
        }

        return $dirs;
    }

    private function isStartsWith(string $composerNs, string $targetNs): bool
    {
        if (strlen($composerNs) > strlen($targetNs)) {
            return str_starts_with($composerNs, $targetNs);
        }

        return str_starts_with($targetNs, $composerNs);
    }

    private function getClass(array $ns, SplFileInfo $f): ?ReflectionClass
    {
        try {
            $namespace = trim($ns[0], '\\') . str_replace([$ns[2], '/'], ['', '\\'], $f->getPath());
            return new ReflectionClass($namespace . '\\' . $f->getBasename('.php'));
        } catch (Throwable) {
            return null;
        }
    }
}