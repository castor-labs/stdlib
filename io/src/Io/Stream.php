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
 * Class Stream represents a read-write temporary stream.
 */
final class Stream extends PhpResource
{
    /**
     * Creates an in-memory buffer of bytes.
     *
     * The reading position starts at offset zero.
     *
     * @throws Error
     */
    public static function memory(string $string = ''): Stream
    {
        $buffer = self::make(\fopen('php://memory', 'w+b'));
        if ('' === $string) {
            return $buffer;
        }

        $buffer->write($string);
        $buffer->seek(0, SEEK_START);

        return $buffer;
    }

    /**
     * Creates a Stream from a resource.
     *
     * @param resource $resource
     *
     * @throws Error
     */
    public static function create($resource): Stream
    {
        return self::make($resource);
    }

    public static function open(string $filename, string $mode): Stream
    {
        return self::make(\fopen($filename, $mode));
    }
}
