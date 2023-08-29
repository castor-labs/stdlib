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

/**
 * Nil represents the Nil or Empty UUID.
 *
 * This is a special UUID that is guaranteed not to be unique and has all of its bits set to 0
 */
final class Nil extends Any
{
    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function create(): Nil
    {
        return new self(Bytes::fromHex(self::NIL_UUID));
    }
}
