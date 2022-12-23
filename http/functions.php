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

use Castor\Context;

/**
 * @internal
 */
const CTX_PARSED_COOKIES = 'http.parsed_cookies';

/**
 * Stores parsed cookies in the context.
 */
function withParsedCookies(Context $ctx, Cookies $cookies): Context
{
    return Context\withValue($ctx, CTX_PARSED_COOKIES, $cookies);
}

/**
 * Returns the parsed cookies stored in the context.
 */
function getParsedCookies(Context $ctx): Cookies
{
    return $ctx->value(CTX_PARSED_COOKIES) ?? Cookies::create();
}

/**
 * Sets the cookie in the header.
 *
 * This method is usually called on Headers from a Response or a ResponseWriter
 *
 * Invalid cookies MAY be silently dropped.
 */
function setCookie(Headers $headers, Cookie $cookie): void
{
    $string = $cookie->toSetCookieString();
    if ('' !== $string) {
        $headers->add(Cookies::SET_COOKIE_HEADER, $string);
    }
}
