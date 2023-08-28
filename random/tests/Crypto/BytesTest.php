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

namespace Castor\Crypto;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class BytesTest extends TestCase
{
    public function testFromUInt8(): void
    {
        $this->assertSame('0f40f0', Bytes::fromUint8(15, 64, 240)->toHex());
    }

    public function testToUint8(): void
    {
        $this->assertSame([15, 64, 240], Bytes::fromHex('0f40f0')->toUint8Array());
    }
}
