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

use Castor\Io\Reader;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RandomTest extends TestCase
{
    /**
     * @required
     */
    public function testItGlobalReturnsSameInstance(): void
    {
        $random = Random::global();
        $random2 = Random::global();
        $random3 = new Random();

        $this->assertSame($random, $random2);
        $this->assertNotSame($random, $random3);
    }

    /**
     * @required
     */
    public function testItReadsFromRandomSource(): void
    {
        $random = Random::global();

        $bytes = $random->read(16);
        $bytes2 = $random->read(16);

        $this->assertNotSame($bytes, $bytes2);
    }

    /**
     * @required
     */
    public function testRandomImplementsReader(): void
    {
        $random = Random::global();

        $this->assertInstanceOf(Reader::class, $random);
    }
}
