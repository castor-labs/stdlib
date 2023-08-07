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

/**
 * Bytes is a value object that provides convenience operations over a byte-string.
 */
final class Bytes implements Io\Reader
{
    public function __construct(
        private readonly string $bytes
    ) {
    }

    public static function fromUint8(int ...$uint8): self
    {
        return new self(\implode('', \array_map('chr', $uint8)));
    }

    /**
     * @throws InputError
     */
    public static function fromHex(string $hex): Bytes
    {
        $bytes = Hex\decode($hex);

        return new self($bytes);
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
}
