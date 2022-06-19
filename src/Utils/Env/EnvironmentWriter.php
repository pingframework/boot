<?php

/**
 * Context
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
 * @package   beachbum\context
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Boot\Utils\Env;

use Pingframework\Boot\Annotations\Service;

/**
 * Environment writer.
 *
 * @package   beachbum\context
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(EnvironmentWriterInterface::class)]
class EnvironmentWriter implements EnvironmentWriterInterface
{
    /**
     * Applies environment variables from file.
     *
     * @throws EnvironmentWriterException
     */
    public function fromFile(string $filePath): array
    {
        $fc = file_get_contents($filePath);
        if ($fc === false) {
            throw new EnvironmentWriterException("Can not read env file [$filePath]");
        }
        $vars = [];
        foreach (explode("\n", $fc) as $line) {
            $line = trim($line);
            if (!empty($line)) {
                putenv($line);
                [$key, $val] = explode('=', $line);
                $vars[$key] = $val;
            }
        }
        return $vars;
    }
}
