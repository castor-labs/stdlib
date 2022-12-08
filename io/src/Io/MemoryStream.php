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

namespace Castor\Io;

/**
 * Class Memory represents a read-write temporary stream.
 *
 * It uses php://memory under the hood.
 */
final class MemoryStream extends PhpStream
{
    /**
     * Creates an in-memory buffer of bytes.
     *
     * The reading position starts at offset zero.
     *
     * @throws Error
     */
    public static function from(string $string): MemoryStream
    {
        $buffer = self::make(fopen('php://memory', 'w+b'));
        $buffer->write($string);
        $buffer->seek(0, SEEK_START);

        return $buffer;
    }
}
