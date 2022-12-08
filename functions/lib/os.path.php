<?php

declare(strict_types=1);

/**
 * @project The Castor Standard Library
 * @link https://github.com/castor-labs/stdlib
 * @package castor/stdlib
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2022 CastorLabs Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Os\Path;

use Castor\Str;

/**
 * The separator for operating system paths.
 */
const SEP = DIRECTORY_SEPARATOR;

function dirname(string $path, int $levels = 0): string
{
    if (0 === $levels) {
        return \realpath($path);
    }

    return \dirname($path, $levels);
}

function join(string $base, string ...$parts): string
{
    return dirname($base).SEP.Str\join($parts, SEP);
}
