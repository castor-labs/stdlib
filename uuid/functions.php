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

use Castor\Uuid;

/**
 * @throws ParsingError
 */
function parse(string $uuid): Uuid
{
    return Any::parse($uuid);
}

function isValid(string $uuid): bool
{
    try {
        parse($uuid);

        return true;
    } catch (ParsingError) {
        return false;
    }
}

namespace Castor\Uuid\Ns;

use Castor\Crypto\Bytes;
use Castor\Uuid;
use Castor\Uuid\Any;

/**
 * Returns the UUID namespace for Domain Name System (DNS).
 */
function dns(): Uuid
{
    static $uuid = null;
    if (null === $uuid) {
        $uuid = Any::fromBytes(Bytes::fromUint8(
            0x6B,
            0xA7,
            0xB8,
            0x10,
            0x9D,
            0xAD,
            0x11,
            0xD1,
            0x80,
            0xB4,
            0x00,
            0xC0,
            0x4F,
            0xD4,
            0x30,
            0xC8,
        ));
    }

    return $uuid;
}

/**
 * Return the UUID namespace for ISO Object Identifiers (OIDs).
 */
function oid(): Uuid
{
    static $uuid = null;
    if (null === $uuid) {
        $uuid = Any::fromBytes(Bytes::fromUint8(
            0x6B,
            0xA7,
            0xB8,
            0x12,
            0x9D,
            0xAD,
            0x11,
            0xD1,
            0x80,
            0xB4,
            0x00,
            0xC0,
            0x4F,
            0xD4,
            0x30,
            0xC8,
        ));
    }

    return $uuid;
}

/**
 * Returns the UUID namespace for Uniform Resource Locators (URLs).
 */
function url(): Uuid
{
    static $uuid = null;
    if (null === $uuid) {
        $uuid = Any::fromBytes(Bytes::fromUint8(
            0x6B,
            0xA7,
            0xB8,
            0x11,
            0x9D,
            0xAD,
            0x11,
            0xD1,
            0x80,
            0xB4,
            0x00,
            0xC0,
            0x4F,
            0xD4,
            0x30,
            0xC8,
        ));
    }

    return $uuid;
}

/**
 * UUID namespace for X.500 Distinguished Names (DNs).
 */
function x500(): Uuid
{
    static $uuid = null;
    if (null === $uuid) {
        $uuid = Any::fromBytes(Bytes::fromUint8(
            0x6B,
            0xA7,
            0xB8,
            0x14,
            0x9D,
            0xAD,
            0x11,
            0xD1,
            0x80,
            0xB4,
            0x00,
            0xC0,
            0x4F,
            0xD4,
            0x30,
            0xC8,
        ));
    }

    return $uuid;
}
