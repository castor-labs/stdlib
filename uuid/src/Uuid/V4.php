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
 */
final class V4 extends Base
{
    /**
     * @throws ParsingError
     */
    public static function parse(string $uuid): self
    {
        $v4 = self::parseVersion($uuid);
        if (!$v4 instanceof self) {
            throw new ParsingError('Not a valid version 4 UUID.');
        }

        return $v4;
    }

    public static function generate(Reader $random = null): self
    {
        $random = $random ?? Random::global();

        $bytes = $random->read(self::BYTES_LENGTH);

        // We set the version to 4
        $bytes[self::VERSION_BYTE] = \chr(\ord($bytes[self::VERSION_BYTE]) & 0x0F | 0x40);
        // Set buts 6-7 to 10
        $bytes[self::VARIANT_BYTE] = \chr(\ord($bytes[self::VARIANT_BYTE]) & 0x3F | 0x80);

        return new self(new Bytes($bytes));
    }
}
