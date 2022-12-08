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

use Castor\Io;

/**
 * Represents the Response Body.
 *
 * It is used internally by he StreamTransport
 *
 * @internal
 */
final class StreamBody extends Io\PhpStream
{
    public static function from($resource): StreamBody
    {
        return self::make($resource);
    }
}
