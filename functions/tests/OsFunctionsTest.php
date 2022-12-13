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

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversDefaultClass
 */
class OsFunctionsTest extends TestCase
{
    public function testOsMakeDir(): void
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage('Permission denied');
        Os\makeDir('/usr/bin/poop');
    }
}
