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
    protected const NIL_UUID = '00000000000000000000000000000000';
    protected const HEX_LENGTH = 32;
    protected const BYTES_LENGTH = 16;
    protected const VERSION_BYTE = 6;
    protected const VARIANT_BYTE = 8;
    protected const VERSION_HEX = 6 * 2;

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

    /**
     * @throws ParsingError
     */
    protected static function parseVersion(string $uuid): Uuid
    {
        $hex = \str_replace('-', '', \strtolower(\trim($uuid)));

        if (self::HEX_LENGTH !== \strlen($hex)) {
            throw new ParsingError(\sprintf(
                'Invalid hexadecimal length for UUID. Expected %d characters with no slashes but got %d.',
                self::HEX_LENGTH,
                \strlen($hex)
            ));
        }

        try {
            $bytes = Bytes::fromHex($hex);
        } catch (InputError $e) {
            throw new ParsingError('Invalid hexadecimal in UUID.', previous: $e);
        }

        if (self::NIL_UUID === $hex) {
            return new Nil($bytes);
        }

        $version = $hex[self::VERSION_HEX];

        return match ($version) {
            '3' => new V3($bytes),
            '4' => new V4($bytes),
            '5' => new V5($bytes),
            default => new Unknown($bytes),
        };
    }
}
