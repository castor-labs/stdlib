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

namespace Castor\Uuid\System;

use Castor\Crypto\Bytes;

interface MacProvider
{
    /**
     * Returns an array of mac addresses parsed from bytes.
     *
     * Implementors MUST NOT return an empty array.
     *
     * In case is possible for the implementation not to be able to find any MAC, you must compose a
     * MacProvider\RandUniMultiCast as a fallback.
     *
     * @return Bytes[]
     */
    public function getMacAddresses(): array;
}
