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

namespace Castor\Io;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Io\MemoryStream
 * @covers \Castor\Io\PhpStream
 */
class MemoryStreamTest extends TestCase
{
    /**
     * @throws EndOfFile
     */
    public function testReading(): void
    {
        $buffer = MemoryStream::from('hello world');
        $read = $buffer->read(1024);
        $this->assertSame('hello world', $read);
        $this->expectException(EndOfFile::class);
        $buffer->read(1024);
    }
}
