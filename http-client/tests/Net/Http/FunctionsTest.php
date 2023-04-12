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

namespace Castor\Net\Http;

use Castor;
use Castor\Context;
use Castor\Crypto\Hash;
use Castor\Io;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Net\Http\Client
 * @covers \Castor\Net\Http\StreamTransport
 * @covers \Castor\Net\Http\TLSConfig
 */
class FunctionsTest extends TestCase
{
    public function testGet(): void
    {
        $response = Castor\Net\Http\get(Context\nil(), 'https://gpg.mnavarro.dev');
        $hash = Hash::MD5->new();
        Io\copy($response->body, $hash);

        $this->assertSame('96b77057aa2c11fd79cd5d4be1fd5c0d', $hash->hashHex());
    }
}
