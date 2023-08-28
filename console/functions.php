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

namespace Castor\Console;

use Castor\Context;

/**
 * @internal
 */
enum CtxKey
{
    /** @internal  */
    case ARGS;
    /** @internal  */
    case EXECUTABLE;
    /** @internal  */
    case BIN_NAME;
}

function withBinName(Context $ctx, string $binName): Context
{
    return Context\withValue($ctx, CtxKey::BIN_NAME, $binName);
}

function getBinName(Context $ctx): ?string
{
    return $ctx->value(CtxKey::BIN_NAME);
}

/**
 * @param string[] $args
 */
function withArgs(Context $ctx, array $args): Context
{
    return Context\withValue($ctx, CtxKey::ARGS, $args);
}

/**
 * @return string[]
 */
function getArgs(Context $ctx): array
{
    return $ctx->value(CtxKey::ARGS) ?? [];
}

function withExecutable(Context $ctx, Executable $executable): Context
{
    return Context\withValue($ctx, CtxKey::EXECUTABLE, $executable);
}

function getExecutable(Context $ctx): Executable
{
    return $ctx->value(CtxKey::EXECUTABLE);
}
