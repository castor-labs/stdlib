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

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Castor\Net\Http\Cookie
 */
class CookieTest extends TestCase
{
    public function testItParsesCookies(): void
    {
        $string = 'preferred_color_mode=light; tz=Europe%2FLondon; _device_id=6b74cceb75d0ad8a8ca8e57ec82a549c; user_session=57c0ed3dcd4767b385d7a240df0b9d8e;';
        $cookies = Cookie::fromCookieString($string);

        $this->assertCount(4, $cookies);
        $this->assertSame('preferred_color_mode', $cookies[0]->name);
        $this->assertSame('light', $cookies[0]->value);
        $this->assertSame('tz', $cookies[1]->name);
        $this->assertSame('Europe/London', $cookies[1]->value);
        $this->assertSame('_device_id', $cookies[2]->name);
        $this->assertSame('6b74cceb75d0ad8a8ca8e57ec82a549c', $cookies[2]->value);
    }

    public function testItParsesSetCookie(): void
    {
        $string = 'sess=57c0ed3dcd4767b385d7a240df0b9d8e; Domain=localhost:8000; Secure; HttpOnly';
        $cookie = Cookie::fromSetCookieString($string);

        $this->assertSame('sess', $cookie->name);
        $this->assertSame('57c0ed3dcd4767b385d7a240df0b9d8e', $cookie->value);
        $this->assertSame('localhost:8000', $cookie->domain);
        $this->assertSame('', $cookie->path);
        $this->assertTrue($cookie->secure);
        $this->assertTrue($cookie->httpOnly);
        $this->assertSame(0, $cookie->maxAge);
    }

    public function testItFailsToParseEmptySetCookie(): void
    {
        $cookie = Cookie::fromSetCookieString('');
        $this->assertSame('', $cookie->name);
    }

    public function testItFailsToParseInvalidDateSetCookie(): void
    {
        $cookie = Cookie::fromSetCookieString('sess=57c0ed3dcd4767b385d7a240df0b9d8e; Expires=1243243223; Secure; HttpOnly');
        $this->assertSame('sess', $cookie->name);
        $this->assertSame('57c0ed3dcd4767b385d7a240df0b9d8e', $cookie->value);
        $this->assertNull($cookie->expires);
        $this->assertSame('', $cookie->path);
        $this->assertTrue($cookie->secure);
        $this->assertTrue($cookie->httpOnly);
    }

    public function testItParsesEmptyCookie(): void
    {
        $cookies = Cookie::fromCookieString('');

        $this->assertCount(0, $cookies);
    }
}
