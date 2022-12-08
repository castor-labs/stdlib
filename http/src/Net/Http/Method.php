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

enum Method: string
{
    case GET = 'GET';

    case POST = 'POST';

    case PUT = 'PUT';

    case PATCH = 'PATCH';

    case DELETE = 'DELETE';

    case OPTIONS = 'OPTIONS';

    case HEAD = 'HEAD';

    case TRACE = 'TRACE';

    case CONNECT = 'CONNECT';

    public function isSafe(): bool
    {
        return match ($this) {
            self::GET, self::HEAD, self::TRACE, self::OPTIONS => true,
            default => false
        };
    }

    public function isIdempotent(): bool
    {
        return match ($this) {
            self::POST, self::PATCH => false,
            default => true,
        };
    }
}
