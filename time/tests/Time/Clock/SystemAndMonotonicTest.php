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

namespace Castor\Time\Clock;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SystemAndMonotonicTest extends TestCase
{
    public function testTimesAreDifferent(): void
    {
        $monotonic = Monotonic::global()->now();
        $system = System::global()->now();

        $monotonicFmt = $monotonic->format('U u');
        $systemFmt = $system->format('U u');

        $this->assertNotEquals($monotonic, $system);
        $this->assertNotSame($monotonicFmt, $systemFmt);
    }
}