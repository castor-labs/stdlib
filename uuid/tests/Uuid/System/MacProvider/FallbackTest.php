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

namespace Castor\Uuid\System\MacProvider;

use Castor\Crypto\Random;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FallbackTest extends TestCase
{
    public function testRandomGeneration(): void
    {
        $macProvider = new Fallback(Random::global());
        $addresses = $macProvider->getMacAddresses();
        $this->assertCount(1, $addresses);
        $address = $addresses[0];

        // The least significant bit of the first octet must always be set
        $this->assertStringEndsWith('1', \base_convert((string) $address[0], 10, 2));
    }
}
