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

function md5_hex(string $string): string
{
    return \md5($string);
}

function md5(string $string): string
{
    return \md5($string, true);
}

function sha1_hex(string $string): string
{
    return \sha1($string);
}

function sha1(string $string): string
{
    return \sha1($string, true);
}
