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

namespace Castor\Context;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Context\KVPair
 */
class KVPairTest extends TestCase
{
    public function testDebug(): void
    {
        $ctx = nil();
        $ctx = withValue($ctx, 'foo', 'bar');
        $ctx = withValue($ctx, 'bar', 'foo');

        $chain = KVPair::debug($ctx);

        $this->assertSame([
            ['bar', 'foo'],
            ['foo', 'bar'],
        ], $chain);
    }
}
