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

namespace Castor\Debug\Logger;

use Castor\Crypto\Hash;
use Castor\Debug\LevelLogger;
use Castor\Io;
use Castor\Io\Stream;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Debug\LevelLogger
 * @covers \Castor\Debug\Logger\Standard
 */
class StandardTest extends TestCase
{
    public function testDefaultPublicApi(): void
    {
        $stream = Stream::memory();

        $logger = Standard::default($stream);
        $logger = new LevelLogger($logger);

        $meta = ['correlation_id' => 'b611627d-f9c6-4350-bbd0-6b4e6e270d29'];

        $logger->warn('This is a warning', $meta);

        $logger->error('This is 30 seconds later');

        $logger->trace('This log should not appear', $meta, [
            'data' => 'foo',
        ]);

        $logger->trace('This log should not appear', $meta, [
            'data' => 'foo',
        ]);

        $logger->fatal('This log is {place}', $meta, [
            'place' => 'using a placeholder',
        ]);

        $logger->info('This log should is {place}', $meta, [
            'place' => ['params' => 'hello'],
        ]);

        $contents = Io\readAll($stream);
        $hash = Hash\md5_hex($contents);

        $this->assertSame('be734a9f030117d5a664741dfafc7fee', $hash);
    }
}
