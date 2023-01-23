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
 * @covers \Castor\Net\Http\Cookies
 */
class CookiesTest extends TestCase
{
    public function testItReadsFromCookieHeader(): void
    {
        $headers = new Headers();
        $headers->set(Cookies::COOKIE_HEADER, 'preferred_color_mode=light; tz=Europe%2FLondon; _device_id=6b74cceb75d0ad8a8ca8e57ec82a549c; user_session=57c0ed3dcd4767b385d7a240df0b9d8e;');

        $cookies = Cookies::fromCookieHeader($headers);

        $this->assertTrue($cookies->has('preferred_color_mode'));
        $this->assertTrue($cookies->has('tz'));
        $this->assertTrue($cookies->has('_device_id'));
        $this->assertTrue($cookies->has('user_session'));
    }

    public function testItReadsFromSetCookieHeader(): void
    {
        $headers = new Headers();
        $headers->add(Cookies::SET_COOKIE_HEADER, 'sess=57c0ed3dcd4767b385d7a240df0b9d8e; Path=/; Max-Age=3600; Secure; HttpOnly');
        $headers->add(Cookies::SET_COOKIE_HEADER, 'rem_tok=57c0ed3dcd4767b385d7a240df0b9d8e; Path=/; Max-Age=424323; Secure; HttpOnly');

        $cookies = Cookies::fromSetCookieHeader($headers);

        $this->assertTrue($cookies->has('sess'));
        $this->assertTrue($cookies->has('rem_tok'));
    }

    public function testItWritesToCookieHeader(): void
    {
        $headers = new Headers();
        $cookies = Cookies::create(new Cookie('foo', 'bar'), new Cookie(''), new Cookie('bar', 'foo'));
        $cookies->writeCookie($headers);
        $this->assertSame('foo=bar; bar=foo', $headers->get(Cookies::COOKIE_HEADER));
    }

    public function testItWritesToSetCookieHeader(): void
    {
        $headers = new Headers();
        $cookies = Cookies::create(new Cookie('foo', 'bar', httpOnly: true), new Cookie(''), new Cookie('bar', 'foo', maxAge: 3600));
        $cookies->writeSetCookie($headers);
        $this->assertSame([
            'foo=bar; HttpOnly',
            'bar=foo; Max-Age=3600',
        ], $headers->values(Cookies::SET_COOKIE_HEADER));
    }
}
