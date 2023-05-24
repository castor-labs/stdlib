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

const SEEK_START = 0;
const SEEK_CURRENT = 1;
const SEEK_END = 2;

/**
 * Reads from a reader until EndOfFile is reached and puts all the contents into
 * memory.
 *
 * @psalm-param positive-int $chunk
 *
 * @throws Error
 */
function readAll(Reader $reader, int $chunk = 4096): string
{
    $contents = '';

    if ($reader instanceof Seeker) {
        $reader->seek(0, SEEK_START);
    }

    while (true) {
        try {
            $contents .= $reader->read($chunk);
        } catch (EndOfFile) {
            break;
        }
    }

    return $contents;
}

/**
 * Copies bytes from a reader to a writer.
 *
 * @psalm-param positive-int $chunk
 *
 * @return positive-int The amount of bytes copied
 *
 * @throws Error
 */
function copy(Reader $reader, Writer $writer, int $chunk = 4096): int
{
    if ($reader instanceof WriterTo) {
        return $reader->writeTo($writer);
    }

    if ($writer instanceof ReaderFrom) {
        return $writer->readFrom($reader);
    }

    $copied = 0;
    while (true) {
        try {
            $bytes = $reader->read($chunk);
            $copied += $writer->write($bytes);
        } catch (EndOfFile) {
            break;
        }
    }

    return $copied;
}
