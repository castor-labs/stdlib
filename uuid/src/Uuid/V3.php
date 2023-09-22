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
use Castor\Uuid;

/**
 * V3 represents a version 3 UUID.
 *
 * Version 3 UUIDS are the md5 hash of another UUID (namespace) plus any string
 *
 * Version 3 UUIDs have their most significant bits on the 7th octet set to 0011 (x30)
 */
final class V3 extends Any
{
    private const HASHING_ALGO = 'md5';

    /**
     * Parses a UUID Version 3 from the string representation.
     *
     * @throws ParsingError
     */
    public static function parse(string $uuid): self
    {
        $v3 = parent::parse($uuid);
        if (!$v3 instanceof self) {
            throw new ParsingError('Not a valid version 3 UUID.');
        }

        return $v3;
    }

    /**
     * Creates a UUID Version 3 from the raw bytes.
     */
    public static function fromBytes(Bytes|string $bytes): self
    {
        $uuid = parent::fromBytes($bytes);
        if (!$uuid instanceof self) {
            throw new ParsingError('Not a valid version 3 UUID.');
        }

        return $uuid;
    }

    public static function create(Uuid $namespace, string $name): self
    {
        $bytes = new Bytes(@\hash(self::HASHING_ALGO, $namespace->getBytes()->asString().$name, true));

        // We set the 7th octet to 0011 XXXX (version 3)
        $bytes[self::VEB] = $bytes[self::VEB] & 0x0F | 0x30; // AND 0000 1111 OR 0011 0000

        // Set buts 6-7 to 10
        $bytes[self::VAB] = $bytes[self::VAB] & 0x3F | 0x80;

        return new self($bytes);
    }
}
