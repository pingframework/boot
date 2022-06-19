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
 * @author    Danny Damsky <danny@bbumgames.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Boot\Utils\Env;

/**
 * Environment Reader Interface
 *
 * @package   beachbum\context
 * @author    Danny Damsky <danny@bbumgames.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
interface EnvironmentReaderInterface
{
    /**
     * Retrieve the given key and return a string value.
     *
     * @throws EnvironmentValueNotFoundException
     */
    public function mustGetString(string $key): string;

    /**
     * Retrieve the given key and return a string or null value.
     */
    public function mayGetString(string $key): ?string;
}
