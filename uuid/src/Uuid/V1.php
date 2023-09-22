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
use Castor\Uuid\V1\GregorianTime;
use Castor\Uuid\V1\Simplified;
use Castor\Uuid\V1\State;

/**
 * V1 represents a version 1 UUID.
 *
 * Version 1 UUIDS are made of a Gregorian Timestamp, a Clock Sequence and one the MAC addresses of the host.
 *
 * Version 1 UUIDs have their most significant bits on the 7th octet set to 0001 (x10)
 */
final class V1 extends Any
{
    /**
     * Parses a UUID Version 3 from the string representation.
     *
     * @throws ParsingError
     */
    public static function parse(string $uuid): self
    {
        $v1 = parent::parse($uuid);
        if (!$v1 instanceof self) {
            throw new ParsingError('Not a valid version 1 UUID.');
        }

        return $v1;
    }

    /**
     * Creates a UUID Version 3 from the raw bytes.
     */
    public static function fromBytes(Bytes|string $bytes): self
    {
        $uuid = parent::fromBytes($bytes);
        if (!$uuid instanceof self) {
            throw new ParsingError('Not a valid version 1 UUID.');
        }

        return $uuid;
    }

    public static function generate(State $state = null): self
    {
        $state = $state ?? Simplified::global();
        $ts = $state->getTime()->bytes->asString();
        $node = $state->getNode()->asString();
        $seq = $state->getClockSequence()->asString();

        $bytes = new Bytes(
            $ts[4].
            $ts[5].
            $ts[6].
            $ts[7].
            $ts[2].
            $ts[3].
            $ts[0].
            $ts[1].
            $seq.
            $node
        );

        // We set the 7th octet to 0001 XXXX (version 1)
        $bytes[self::VEB] = $bytes[self::VEB] & 0x0F | 0x10; // AND 0000 1111 OR 0001 0000

        // Set buts 6-7 to 10
        $bytes[self::VAB] = $bytes[self::VAB] & 0x3F | 0x80;

        return new self($bytes);
    }

    /**
     * Returns the Gregorian Time of this UUID.
     */
    public function getTime(): GregorianTime
    {
        $bytes = $this->getBytes();
        $bytes[6] = $bytes[6] & 0x0F; // Unset the version bits
        $b = $bytes->asString();
        $bytes = new Bytes($b[6].$b[7].$b[4].$b[5].$b[0].$b[1].$b[2].$b[3]);

        return new GregorianTime($bytes);
    }

    /**
     * Returns the node of this UUID.
     */
    public function getNode(): Bytes
    {
        return $this->getBytes()->slice(10);
    }

    /**
     * Returns the clock sequence of this UUID.
     */
    public function getClockSeq(): Bytes
    {
        $bytes = $this->getBytes();
        $bytes[8] = $bytes[8] & 0x3F; // Unset the variant bits

        return $bytes->slice(8, 2);
    }
}
