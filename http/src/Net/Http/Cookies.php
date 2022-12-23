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
use Castor\Str;

class Cookies
{
    private const COOKIE_HEADER = 'Cookie';
    private const SET_COOKIE_HEADER = 'Set-Cookie';

    /**
     * @var array<string,Cookie>
     */
    private array $cookies;

    private function __construct()
    {
        $this->cookies = [];
    }

    /**
     * Parses the cookies from the response.
     */
    public static function fromResponse(Response $response): Cookies
    {
        $headers = $response->headers->values(self::SET_COOKIE_HEADER);
        $cookies = Arr\map($headers, Cookie::fromSetCookieString(...));

        return self::create(...$cookies);
    }

    public static function create(Cookie ...$cookies): Cookies
    {
        $jar = new self();
        foreach ($cookies as $cookie) {
            $jar->cookies[$cookie->name] = $cookie;
        }

        return $jar;
    }

    /**
     * Parses the cookies from the request.
     */
    public static function fromRequest(Request $request): Cookies
    {
        $string = $request->headers->get(self::COOKIE_HEADER);
        $cookies = Cookie::fromCookieString($string);

        return self::create(...$cookies);
    }

    public function get(string $name): Cookie
    {
        return $this->lookup($name) ?? new Cookie($name);
    }

    public function lookup(string $name): ?Cookie
    {
        return $this->cookies[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->cookies);
    }

    /**
     * @return Cookie[]
     */
    public function all(): array
    {
        return array_values($this->cookies);
    }

    public function with(Cookie $cookie): Cookies
    {
        $clone = clone $this;
        $clone->cookies[$cookie->name] = $cookie;

        return $clone;
    }

    public function without(string $name): Cookies
    {
        $clone = clone $this;

        if ($this->has($name)) {
            unset($clone->cookies[$name]);
        }

        return $clone;
    }

    /**
     * Writes the cookies into the response.
     */
    public function toResponse(Response $response): void
    {
        $response->headers->del(self::SET_COOKIE_HEADER);
        foreach ($this->cookies as $cookie) {
            $response->headers->add(self::SET_COOKIE_HEADER, $cookie->toSetCookieString());
        }
    }

    /**
     * Writes the cookies into the request.
     */
    public function toRequest(Request $request): void
    {
        $string = Str\join(Arr\map($this->cookies, static fn (Cookie $c) => $c->toCookieString()), '; ');
        $request->headers->set(self::COOKIE_HEADER, $string);
    }
}
