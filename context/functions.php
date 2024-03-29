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

namespace Castor\Context;

use Castor\AlreadyRegistered;
use Castor\Context;

/**
 * Stores a value in a context under the specified key.
 */
function withValue(Context $ctx, mixed $key, mixed $value): Context
{
    return new KVPair($ctx, $key, $value);
}

/**
 * Returns a Context that always returns null.
 */
function nil(): Context
{
    return new Value();
}

/**
 * @internal
 */
enum Key
{
    case CANCEL;
}

/**
 * Stores a cancellation signal in the context.
 *
 * @param callable():bool $signal
 *
 * @throws AlreadyRegistered if the context already contains a cancellation signal
 */
function withCancel(Context $ctx, callable $signal): Context
{
    if (null !== $ctx->value(Key::CANCEL)) {
        throw new AlreadyRegistered('This context holds a cancellation signal already');
    }

    return withValue($ctx, Key::CANCEL, $signal);
}

/**
 * Checks whether a context is cancelled.
 *
 * By default, if no cancellation callable was stored, the context is not cancelled.
 */
function isCancelled(Context $ctx): bool
{
    $signal = $ctx->value(Key::CANCEL);
    if (\is_callable($signal)) {
        return (bool) $signal();
    }

    return false;
}
