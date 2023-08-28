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

use Castor\Encoding\Hex;
use Castor\Encoding\InputError;
use Castor\Io;
use Castor\Str;

use function Castor\Bytes\len;

/**
 * Bytes is a value object that provides convenience operations over a byte-string.
 *
 * @implements \ArrayAccess<int,int>
 */
class Bytes implements Io\Reader, \ArrayAccess
{
    public function __construct(
        protected string $bytes
    ) {
    }

    public static function fromUint8(int ...$uint8): static
    {
        return new static(\implode('', \array_map('chr', $uint8)));
    }

    /**
     * @throws InputError
     */
    public static function fromHex(string $hex): static
    {
        $bytes = Hex\decode($hex);

        return new static($bytes);
    }

    public function asString(): string
    {
        return $this->bytes;
    }

    public function read(int $length): string
    {
        if ($length > \strlen($this->bytes)) {
            throw new Io\Error('Not enough bytes to read');
        }

        return \substr($this->bytes, 0, $length);
    }

    public function toHex(): string
    {
        return Hex\encode($this->bytes);
    }

    public function equals(Bytes $bytes): bool
    {
        return $this->bytes === $bytes->bytes;
    }

    /**
     * @return int[]
     */
    public function toUint8Array(): array
    {
        return \array_values(\unpack('C*', $this->bytes));
    }

    public function len(): int
    {
        return len($this->bytes);
    }

    /**
     * @param int $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        if (!\is_int($offset)) {
            throw new \InvalidArgumentException('Offset must be an int');
        }

        return ($this->bytes[$offset] ?? null) !== null;
    }

    /**
     * @param int $offset
     */
    public function offsetGet(mixed $offset): int
    {
        if (!\is_int($offset)) {
            throw new \InvalidArgumentException('Offset must be an int');
        }

        $b = $this->bytes[$offset] ?? null;
        if (null === $b) {
            throw new \OutOfBoundsException('Offset ');
        }

        return \ord($b);
    }

    /**
     * @param int $offset
     * @param int $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!\is_int($offset)) {
            throw new \InvalidArgumentException('Offset must be an int');
        }

        if (!\is_int($value)) {
            throw new \InvalidArgumentException('Value must be an int');
        }

        if ($value > 255 || $value < 0) {
            throw new \InvalidArgumentException('Value must be an integer between 0 and 255');
        }

        if (!$this->offsetExists($offset)) {
            throw new \OutOfBoundsException("Offset {$offset} does not exist");
        }

        $this->bytes[$offset] = \chr($value);
    }

    /**
     * @param int $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        if (!\is_int($offset)) {
            throw new \InvalidArgumentException('Offset must be an int');
        }

        $this->bytes = Str\slice($this->bytes, 0, $offset).Str\slice($this->bytes, $offset + 1);
    }
}
