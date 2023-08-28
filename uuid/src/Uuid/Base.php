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

namespace Castor\Uuid;

use Castor\Crypto\Bytes;
use Castor\Encoding\InputError;
use Castor\Uuid;

/**
 * Base class for all UUIDs.
 *
 * The stability of this API is not guaranteed. You are discouraged to extend this class.
 *
 * @internal
 */
abstract class Base implements Uuid, \Stringable, \JsonSerializable
{
    /** @var string The hex representation of a Nil UUID */
    protected const NIL_UUID = '00000000000000000000000000000000';

    /** @var string The hex representation of a Max UUID */
    protected const MAX_UUID = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF';

    /** @var int Length of bytes of an UUID */
    protected const LEN = 16;

    /** @var int The version byte */
    protected const VEB = 6;

    /** @var int The variant byte */
    protected const VAB = 8;

    public function __construct(
        private readonly Bytes $bytes,
    ) {
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function __serialize(): array
    {
        return [$this->toString()];
    }

    public function __unserialize(array $data): void
    {
        $this->bytes = Unknown::parse($data[0])->getBytes();
    }

    public function toString(): string
    {
        return \sprintf('%s%s-%s-%s-%s-%s%s%s', ...\str_split($this->bytes->toHex(), 4));
    }

    public function getBytes(): Bytes
    {
        return $this->bytes;
    }

    public function equals(Uuid $uuid): bool
    {
        return $this->bytes->equals($uuid->getBytes());
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function toUrn(): string
    {
        return 'urn:uuid:'.$this->toString();
    }

    protected static function fromBytes(Bytes|string $bytes): Uuid
    {
        if (\is_string($bytes)) {
            $bytes = new Bytes($bytes);
        }

        if (self::LEN !== $bytes->len()) {
            throw new ParsingError('UUID must have 16 bytes.');
        }

        $hex = $bytes->toHex();

        if (self::NIL_UUID === $hex) {
            return new Nil($bytes);
        }

        if (self::MAX_UUID === $hex) {
            return new Max($bytes);
        }

        $v = $bytes[self::VEB] & 0xF0; // 1111 0000

        return match ($v) {
            0x30 => new V3($bytes), // 0011 0000
            0x40 => new V4($bytes), // 0100 0000
            0x50 => new V5($bytes), // 0101 0000
            default => new Unknown($bytes)
        };
    }

    /**
     * @throws ParsingError
     */
    protected static function parse(string $uuid): Uuid
    {
        $hex = \str_replace('-', '', \strtolower(\trim($uuid)));

        try {
            $bytes = Bytes::fromHex($hex);
        } catch (InputError $e) {
            throw new ParsingError('Invalid hexadecimal in UUID.', previous: $e);
        }

        return self::fromBytes($bytes);
    }
}
