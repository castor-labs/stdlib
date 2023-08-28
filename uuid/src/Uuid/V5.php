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
 * V5 represents a version 4 UUID.
 *
 * Version 5 UUIDS are the first 16 bytes of the sha1 hash of another UUID (namespace) plus any string.
 *
 * Version 5 UUIDs have their most significant bits on the 7th octet set to 0101 (x50)
 */
final class V5 extends Base
{
    private const HASHING_ALGO = 'sha1';

    /**
     * @throws ParsingError
     */
    public static function parse(string $uuid): self
    {
        $v5 = parent::parse($uuid);
        if (!$v5 instanceof self) {
            throw new ParsingError('Not a valid version 5 UUID.');
        }

        return $v5;
    }

    public static function fromBytes(Bytes|string $bytes): self
    {
        $uuid = parent::fromBytes($bytes);
        if (!$uuid instanceof self) {
            throw new ParsingError('Not a valid version 5 UUID.');
        }

        return $uuid;
    }

    public static function create(Uuid $namespace, string $name): self
    {
        $bytes = @\hash(self::HASHING_ALGO, $namespace->getBytes()->asString().$name, true);
        $bytes = new Bytes(\substr($bytes, 0, self::LEN));

        // We set the 6th octet to 0101 XXXX (version 5)
        $bytes[self::VEB] = $bytes[self::VEB] & 0x0F | 0x50; // // AND 0000 1111 OR 0101 0000

        // Set buts 6-7 to 10
        $bytes[self::VAB] = $bytes[self::VAB] & 0x3F | 0x80;

        return new self($bytes);
    }
}
