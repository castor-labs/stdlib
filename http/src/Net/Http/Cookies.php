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

use Castor\Arr;
use Castor\Net;
use Castor\Str;

/**
 * Cookies is an indexed collection of Cookie instances.
 */
class Cookies implements \Countable
{
    final public const COOKIE_HEADER = 'Cookie';
    final public const SET_COOKIE_HEADER = 'Set-Cookie';

    /**
     * @var array<string,Cookie>
     */
    private array $cookies;

    /**
     * @psalm-external-mutation-free
     */
    private function __construct()
    {
        $this->cookies = [];
    }

    /**
     * Parses the cookies from the Set-Cookie header.
     *
     * This method is usually used with the headers of a Response or a ResponseWriter.
     *
     * @psalm-external-mutation-free
     */
    public static function fromSetCookieHeader(Headers $headers): static
    {
        $values = $headers->values(self::SET_COOKIE_HEADER);
        $cookies = Arr\map($values, Cookie::fromSetCookieString(...));

        return static::create(...$cookies);
    }

    /**
     * @psalm-external-mutation-free
     */
    public static function create(Cookie ...$cookies): static
    {
        $self = new static();
        foreach ($cookies as $cookie) {
            $self = $self->with($cookie);
        }

        return $self;
    }

    /**
     * Parses the cookies from the Cookie header.
     *
     * This method is usually used with the headers of a Request
     *
     * @psalm-external-mutation-free
     */
    public static function fromCookieHeader(Headers $headers): static
    {
        $string = $headers->get(self::COOKIE_HEADER);
        $cookies = Cookie::fromCookieString($string);

        return static::create(...$cookies);
    }

    /**
     * Returns a cookie from the cookie collection.
     *
     * If the cookie does not exist inside the collection, it returns a newly created cookie with the passed name.
     *
     * The newly created cookie IS NOT stored in the internal collection.
     *
     * @psalm-mutation-free
     */
    public function get(string $name): Cookie
    {
        return $this->lookup($name) ?? new Cookie($name);
    }

    /**
     * Looks up for a cookie and returns it if found. Otherwise, returns null.
     *
     * @psalm-mutation-free
     */
    public function lookup(string $name): ?Cookie
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * @psalm-mutation-free
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->cookies);
    }

    /**
     * @return Cookie[]
     *
     * @psalm-mutation-free
     */
    public function toArray(): array
    {
        return Arr\values($this->cookies);
    }

    /**
     * @return $this
     *
     * @psalm-external-mutation-free
     */
    public function with(Cookie $cookie): static
    {
        $clone = clone $this;
        $clone->cookies[$cookie->name] = $cookie;

        return $clone;
    }

    /**
     * @return $this
     *
     * @psalm-external-mutation-free
     */
    public function without(string $name): static
    {
        $clone = clone $this;

        if ($this->has($name)) {
            unset($clone->cookies[$name]);
        }

        return $clone;
    }

    /**
     * Writes the cookies as Set-Cookie in the headers.
     *
     * This method is usually called on the headers of a Response or a ResponseWriter.
     *
     * This method overrides all the previously set cookies. To set just a single cookie without
     * modifying other cookies, please use the Castor\Net\Http\setCookie function.
     */
    public function writeSetCookie(Headers $headers): void
    {
        $headers->del(self::SET_COOKIE_HEADER);
        foreach ($this->cookies as $cookie) {
            Net\Http\setCookie($headers, $cookie);
        }
    }

    /**
     * Writes the cookies into the Cookie header.
     *
     * This method is usually called on the headers of a Request.
     *
     * It will silently ignore cookies without a name
     */
    public function writeCookie(Headers $headers): void
    {
        $string = Str\join(Arr\filter(Arr\map($this->cookies, static fn (Cookie $c) => $c->toCookieString())), '; ');
        $headers->set(self::COOKIE_HEADER, $string);
    }

    /**
     * @psalm-mutation-free
     */
    public function count(): int
    {
        return \count($this->cookies);
    }
}
