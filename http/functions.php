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
enum ContextKeys
{
    case PARSED_COOKIES;

    case CLIENT_IP;
}

/**
 * Stores parsed cookies in the context.
 */
function withParsedCookies(Context $ctx, Cookies $cookies): Context
{
    return Context\withValue($ctx, ContextKeys::PARSED_COOKIES, $cookies);
}

/**
 * Returns the parsed cookies stored in the context.
 */
function getParsedCookies(Context $ctx): Cookies
{
    return $ctx->value(ContextKeys::PARSED_COOKIES) ?? Cookies::create();
}

/**
 * Stores the client IP in the context.
 */
function withClientIp(Context $ctx, string $ip): Context
{
    return Context\withValue($ctx, ContextKeys::CLIENT_IP, $ip);
}

/**
 * Returns the client ip in the context.
 *
 * If no IP is found, it returns 0.0.0.0
 */
function getClientIp(Context $ctx): string
{
    return $ctx->value(ContextKeys::CLIENT_IP) ?? '0.0.0.0';
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
