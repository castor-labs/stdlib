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

namespace Castor\Uuid\V1;

use Brick\DateTime\Clock\FixedClock;
use Brick\DateTime\Instant;
use Brick\Math\BigInteger;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Uuid\V1\GregorianTime
 */
class GregorianTimeTest extends TestCase
{
    public function testGregorianTime(): void
    {
        $time = GregorianTime::now(new FixedClock(Instant::of(1693426201, 201233)));
        $this->assertSame('139127190010002012', (string) $time->getTimestamp());
    }

    public function testGregorianDatetime(): void
    {
        $instant = GregorianTime::fromTimestamp(BigInteger::of('139127190010002012'))->getInstant();
        $this->assertSame(1693426201, $instant->getEpochSecond());
        $this->assertSame(201200, $instant->getNano()); // Nano precision is lost because of 100 nano intervals
    }
}
