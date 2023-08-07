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
 * This class represents a PhpResource with all the possible operations in it.
 */
abstract class PhpResource implements Reader, Writer, Seeker, Closer, ReaderAt, WriterAt, Flusher
{
    private const FLAG_SEEKABLE = 1;
    private const FLAG_READABLE = 2;
    private const FLAG_WRITABLE = 4;
    private const FLAG_CLOSED = 8;

    /** @var array Hash of readable and writable stream types */
    private const READ_WRITE_HASH = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true,
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true,
        ],
    ];

    /**
     * @var closed-resource|resource
     */
    private $resource;
    private int $flags;

    final private function __construct($resource, int $flags)
    {
        $this->resource = $resource;
        $this->flags = $flags;
    }

    public function close(): void
    {
        if (null === $this->resource) {
            throw new Error('The underlying resource has been detached');
        }

        \fclose($this->resource);
        $this->flags += self::FLAG_CLOSED;
    }

    public function read(int $length): string
    {
        $this->ensureResource();

        if (!$this->isReadable()) {
            throw new Error('The underlying resource is not readable');
        }

        if (\feof($this->resource)) {
            throw new EndOfFile('End of file before read');
        }

        $bytes = \fread($this->resource, $length);

        // This catches eof for streams where is detected after a read, like network streams
        if (0 !== $length && ('' === $bytes || false === $bytes) && \feof($this->resource)) {
            throw new EndOfFile('End of file after read');
        }

        if (!\is_string($bytes)) {
            throw new Error('Unknown error while reading bytes');
        }

        return $bytes;
    }

    public function readAt(int $offset, int $length): string
    {
        $this->seek($offset);

        return $this->read($length);
    }

    public function seek(int $offset = 0, int $whence = SEEK_CURRENT): int
    {
        $this->ensureResource();

        if (!$this->isSeekable()) {
            throw new Error('Underlying stream is not seekable');
        }

        return \fseek($this->resource, $offset, $whence);
    }

    public function write(string $bytes): int
    {
        $this->ensureResource();

        if (!$this->isWritable()) {
            throw new Error('Underlying stream is not writable');
        }

        $int = \fwrite($this->resource, $bytes);

        if (!\is_int($int)) {
            throw new Error('Unknown error while writing bytes');
        }

        return $int;
    }

    public function writeAt(int $offset, string $bytes): int
    {
        $this->seek($offset);

        return $this->write($bytes);
    }

    public function flush(): void
    {
        $this->ensureResource();

        if (!\fflush($this->resource)) {
            throw new Error('Error while flushing buffer');
        }
    }

    public function isReadable(): bool
    {
        return ($this->flags & self::FLAG_READABLE) !== 0;
    }

    public function isWritable(): bool
    {
        return ($this->flags & self::FLAG_WRITABLE) !== 0;
    }

    public function isSeekable(): bool
    {
        return ($this->flags & self::FLAG_SEEKABLE) !== 0;
    }

    public function isClosed(): bool
    {
        return ($this->flags & self::FLAG_CLOSED) !== 0;
    }

    /**
     * @return closed-resource|resource
     */
    public function detach()
    {
        if (null === $this->resource) {
            throw new Error('The underlying resource has already been detached');
        }

        $resource = $this->resource;
        $this->resource = null;

        return $resource;
    }

    /**
     * @param resource $resource
     */
    protected static function make($resource): static
    {
        if (!\is_resource($resource)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Argument 1 passed to %s must be a resource, %s given',
                    __METHOD__,
                    \gettype($resource)
                )
            );
        }

        $meta = \stream_get_meta_data($resource);
        $seekable = $meta['seekable'] && 0 === \fseek($resource, 0, \SEEK_CUR);
        $readable = isset(self::READ_WRITE_HASH['read'][$meta['mode']]);
        $writable = isset(self::READ_WRITE_HASH['write'][$meta['mode']]);

        $flags = 0;
        if ($seekable) {
            $flags += self::FLAG_SEEKABLE;
        }

        if ($readable) {
            $flags += self::FLAG_READABLE;
        }

        if ($writable) {
            $flags += self::FLAG_WRITABLE;
        }

        return new static($resource, $flags);
    }

    private function ensureResource(): void
    {
        if (null === $this->resource) {
            throw new Error('The underlying resource has been detached');
        }

        if ($this->isClosed()) {
            throw new Error('The underlying resource has been closed');
        }
    }
}
