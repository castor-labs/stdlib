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

use Castor\Time\Clock\Frozen;
use Castor\Time\Clock\Monotonic;
use Castor\Time\Clock\System;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class GregorianTimeTest extends TestCase
{
    public function testGregorianTime(): void
    {
        $time = GregorianTime::now(Frozen::at(DATE_ATOM, '2023-08-30T18:24:26+00:00'));
        $this->assertSame('139127126660000000', $time->getTimestamp());
    }

    public function testGregorianDatetime(): void
    {
        $time = GregorianTime::fromTimestamp('139127190012012330')->getDatetime();
        $this->assertSame('1693426201.201233', $time->format('U.u'));
    }

    public function testGregorianTimeWithTwoClocks(): void
    {
        $system = GregorianTime::now(System::global());
        $monotonic = GregorianTime::now(Monotonic::global());

        $this->assertNotSame(
            $system->getTimestamp(),
            $monotonic->getTimestamp()
        );
    }
}
