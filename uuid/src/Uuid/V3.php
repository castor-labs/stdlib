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
 */
final class V3 extends Base
{
    private const HASHING_ALGO = 'md5';

    /**
     * @throws ParsingError
     */
    public static function parse(string $uuid): self
    {
        $v3 = self::parseVersion($uuid);
        if (!$v3 instanceof self) {
            throw new ParsingError('Not a valid version 3 UUID.');
        }

        return $v3;
    }

    public static function create(Uuid $namespace, string $name): self
    {
        $bytes = @\hash(self::HASHING_ALGO, $namespace->getBytes()->asString().$name, true);

        // We set the version to 5
        $bytes[self::VERSION_BYTE] = \chr(\ord($bytes[self::VERSION_BYTE]) & 0x0F | 0x30);

        // Set buts 6-7 to 10
        $bytes[self::VARIANT_BYTE] = \chr(\ord($bytes[self::VARIANT_BYTE]) & 0x3F | 0x80);

        return new self(new Bytes($bytes));
    }
}
