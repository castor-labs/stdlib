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
use Castor\Crypto\Random;
use Castor\Io\Reader;

/**
 * V4 represents a version 4 UUID.
 *
 * Version 4 UUIDs are made of 128 random bits. However, 6 bits are used to indicate the version and the variant.
 * Thus, the actual randomness is 122 bits. The possibilities of collisions are one in 5.3 undecillions.
 *
 * Version 4 UUIDs have their most significant bits on the 7th octet set to 0100 (x40)
 */
final class V4 extends Base
{
    /**
     * @throws ParsingError
     */
    public static function parse(string $uuid): self
    {
        $v4 = parent::parse($uuid);
        if (!$v4 instanceof self) {
            throw new ParsingError('Not a valid version 4 UUID.');
        }

        return $v4;
    }

    public static function fromBytes(Bytes|string $bytes): self
    {
        $uuid = parent::fromBytes($bytes);
        if (!$uuid instanceof self) {
            throw new ParsingError('Not a valid version 4 UUID.');
        }

        return $uuid;
    }

    public static function generate(Reader $random = null): self
    {
        $random = $random ?? Random::global();

        $bytes = new Bytes($random->read(self::LEN));

        // We set the 6th octet to 0100 XXXX (version 4)
        $bytes[self::VEB] = $bytes[self::VEB] & 0x0F | 0x40; // AND 0000 1111 OR 0100 0000
        // Set the variant to 6-7 to 10
        $bytes[self::VAB] = $bytes[self::VAB] & 0x3F | 0x80;

        return new self($bytes);
    }
}
