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

namespace Castor\Debug\Logger\Level;

use Castor\Context;
use Castor\Debug\Logger\Level;

/**
 * @internal
 */
const CTX_KEY = 'logger.level';

/**
 * Adds fatal log level to the context.
 */
function fatal(Context $ctx): Context
{
    return Context\withValue($ctx, CTX_KEY, Level::FATAL);
}

/**
 * Adds error log level to the context.
 */
function error(Context $ctx): Context
{
    return Context\withValue($ctx, CTX_KEY, Level::ERROR);
}

/**
 * Adds warn log level to the context.
 */
function warn(Context $ctx): Context
{
    return Context\withValue($ctx, CTX_KEY, Level::WARN);
}

/**
 * Adds info log level to the context.
 */
function info(Context $ctx): Context
{
    return Context\withValue($ctx, CTX_KEY, Level::INFO);
}

/**
 * Adds debug log level to the context.
 */
function debug(Context $ctx): Context
{
    return Context\withValue($ctx, CTX_KEY, Level::DEBUG);
}

/**
 * Adds trace log level to the context.
 */
function trace(Context $ctx): Context
{
    return Context\withValue($ctx, CTX_KEY, Level::TRACE);
}

/**
 * Gets the level stored in the context.
 */
function get(Context $ctx): ?Level
{
    return $ctx->value(CTX_KEY);
}

namespace Castor\Debug\Logger\Meta;

use Castor\Context;
use Castor\Debug\Logger\Meta;

/**
 * @internal
 */
const CTX_KEY = 'logger.meta';

/**
 * Adds values to the logger metadata.
 */
function withValue(Context $ctx, string $key, mixed $value): Context
{
    $meta = get($ctx);

    if ($meta->isEmpty()) {
        $ctx = Context\withValue($ctx, CTX_KEY, $meta);
    }

    $meta->add($key, $value);

    return $ctx;
}

/**
 * Adds values to the logger metadata.
 *
 * @param array<string,mixed> $values
 */
function withValues(Context $ctx, array $values): Context
{
    $meta = get($ctx);

    if ($meta->isEmpty()) {
        $ctx = Context\withValue($ctx, CTX_KEY, $meta);
    }

    $meta->merge($values);

    return $ctx;
}

/**
 * Adds error log level to the context.
 */
function get(Context $ctx): Meta
{
    return $ctx->value(CTX_KEY) ?? new Meta();
}
