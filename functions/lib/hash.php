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

namespace Castor\Crypto\Hash;

use function md5 as native_md5;
use function sha1 as native_sha1;

/**
 * @psalm-pure
 */
function md5_hex(string $string): string
{
    return native_md5($string);
}

/**
 * @psalm-pure
 */
function md5(string $string): string
{
    return native_md5($string, true);
}

/**
 * @psalm-pure
 */
function sha1_hex(string $string): string
{
    return native_sha1($string);
}

/**
 * @psalm-pure
 */
function sha1(string $string): string
{
    return native_sha1($string, true);
}
