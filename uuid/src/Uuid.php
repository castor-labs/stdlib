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

namespace Castor;

use Castor\Crypto\Bytes;

/**
 * This is the base contract for a UUID.
 *
 * This contract is purposefully simple and it only limits itself to the operations that are common to all UUIDs
 *
 * If you need to act upon a particular implementation (for instance, extract the time of a V1 UUID), you MUST
 * type hint to that particular version of use the "instanceof" operator on this interface.
 */
interface Uuid
{
    /**
     * Returns the underlying bytes that make up this UUID.
     */
    public function getBytes(): Bytes;

    /**
     * Returns the standard segmented hexadecimal representation of the UUID.
     *
     * The format is 00000000-0000-0000-0000-000000000000
     */
    public function toString(): string;

    /**
     * Checks whether two UUIDs are equal.
     */
    public function equals(Uuid $uuid): bool;
}
