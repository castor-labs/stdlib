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

namespace Castor\Net\Http;

/**
 * Version represents the HTTP protocol version.
 */
enum Version: string
{
    case HTTP10 = 'HTTP/1.0';

    case HTTP11 = 'HTTP/1.1';

    case HTTP2 = 'HTTP/2';

    case HTTP20 = 'HTTP/2.0';

    public function major(): int
    {
        return match ($this) {
            self::HTTP10, self::HTTP11 => 1,
            self::HTTP20, self::HTTP2 => 2,
        };
    }

    public function minor(): int
    {
        return match ($this) {
            self::HTTP11 => 1,
            self::HTTP10, self::HTTP20, self::HTTP2 => 0,
        };
    }

    public function toFloat(): float
    {
        return match ($this) {
            self::HTTP10 => 1.0,
            self::HTTP11 => 1.1,
            self::HTTP20, self::HTTP2 => 2.0
        };
    }
}
