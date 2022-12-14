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

namespace Castor\Crypto;

use Castor\Io\Error;
use Castor\Io\Reader;

/**
 * Random implements reader to read random bytes from PHP's random bytes source.
 *
 * You need to be careful because this Reader does not throw EndOfFile errors.
 */
final class Random implements Reader
{
    private static ?Random $global = null;

    public static function global(): Random
    {
        if (null === self::$global) {
            self::$global = new self();
        }

        return self::$global;
    }

    public function read(int $length): string
    {
        try {
            return \random_bytes($length);
        } catch (\Exception $e) {
            throw new Error('Error while reading random bytes', previous: $e);
        }
    }
}
