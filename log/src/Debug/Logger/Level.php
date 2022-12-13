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

namespace Castor\Debug\Logger;

enum Level: int
{
    case FATAL = 5;

    case ERROR = 4;

    case WARN = 3;

    case INFO = 2;

    case DEBUG = 1;

    case TRACE = 0;

    public function toString(): string
    {
        return match ($this) {
            self::FATAL => 'fatal',
            self::ERROR => 'error',
            self::WARN => 'warning',
            self::INFO => 'info',
            self::DEBUG => 'debug',
            self::TRACE => 'trace',
        };
    }
}
