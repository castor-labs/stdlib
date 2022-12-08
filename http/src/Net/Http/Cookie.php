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

class Cookie
{
    public function __construct(
        public string $name,
        public string $value = '',
        public string $path = '',
        public string $domain = '',
        public ?\DateTimeImmutable $expires = null,
        public int $maxAge = 0,
        public bool $secure = false,
        public bool $httpOnly = false,
        public SameSite $sameSite = SameSite::NONE,
    ) {
    }

    /**
     * @return Cookie[]
     */
    public static function all(Request $request): array
    {
        return self::readCookies($request->headers->get('Cookie'));
    }

    /**
     * Gets a cookie from the request.
     *
     * If the cookie does not exist, a new instance with its name
     * its created.
     */
    public static function get(Request $request, string $name): Cookie
    {
        return self::lookup($request, $name) ?? new Cookie($name);
    }

    /**
     * Looks up a Cookie in the request.
     *
     * If the cookie does not exist, then null is returned
     */
    public static function lookup(Request $request, string $name): ?Cookie
    {
        return self::readCookies($request->headers->get('Cookie'), $name)[0] ?? null;
    }

    /**
     * @return Cookie[]
     */
    private static function readCookies(string $value, string $filter = ''): array
    {
        if ('' === $value) {
            return [];
        }

        return [];
    }
}
