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

use Castor\Context;
use Castor\Crypto\Hash;
use Castor\Io;
use Castor\Io\Stream;
use Castor\Time\Clock\Frozen;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class StandardTest extends TestCase
{
    public function testDefaultPublicApi(): void
    {
        $stream = Stream::memory();
        $clock = Frozen::at('Y-m-d H:i:s', '2022-04-05 13:00:00');

        $logger = Standard::default($stream, $clock);

        $ctx = Context\nil();
        $ctx = Meta\withValue($ctx, 'correlation_id', 'b611627d-f9c6-4350-bbd0-6b4e6e270d29');

        $logger->log(Level\warn($ctx), 'This is a warning');

        $clock->advance('PT30S');

        $logger->log(Level\error($ctx), 'This is 30 seconds later');

        $logger->log(Level\trace($ctx), 'This log should not appear');

        $stream->seek(0, Io\SEEK_START);
        $data = Hash\md5_hex(Io\readAll($stream));

        $this->assertSame('ed9936d1723eeef4418b6d89b7cbbf60', $data);
    }
}
