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

use Castor\Arr;
use Castor\Context;

/**
 * @internal
 */
enum Key
{
    case LEVEL;

    case META;

    case APP;
}

/**
 * Adds values to the logger metadata.
 */
function withMeta(Context $ctx, array $meta): Context
{
    $stored = getMeta($ctx);

    return Context\withValue($ctx, Key::META, Arr\merge($stored, $meta));
}

/**
 * Adds error log level to the context.
 */
function getMeta(Context $ctx): array
{
    return $ctx->value(Key::META) ?? [];
}

function withApp(Context $ctx, string $app): Context
{
    return Context\withValue($ctx, Key::APP, $app);
}

function getApp(Context $ctx): string
{
    return $ctx->value(Key::APP) ?? '';
}

/**
 * Adds fatal log level to the context.
 */
function withLevel(Context $ctx, Level $level): Context
{
    return Context\withValue($ctx, Key::LEVEL, $level);
}

/**
 * Gets the level stored in the context.
 */
function getLevel(Context $ctx): ?Level
{
    return $ctx->value(Key::LEVEL);
}
