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

namespace Castor\Net\Http\Routing;

use Castor\Context;
use Castor\MissingContextValue;
use Castor\Net\Http\Routing\Dispatcher\Found;
use Castor\Net\Http\Routing\Dispatcher\MethodNotAllowed;
use Castor\Net\Http\Routing\Dispatcher\NotFound;

/** @internal  */
enum CtxKey
{
    /** @internal  */
    case Result;
}

function withResult(Context $ctx, Found|MethodNotAllowed|NotFound $result): Context
{
    return Context\withValue($ctx, CtxKey::Result, $result);
}

/**
 * @throws MethodNotAllowed
 * @throws NotFound
 */
function getResult(Context $ctx): Found
{
    $result = $ctx->value(CtxKey::Result);
    if ($result instanceof Found) {
        return $result;
    }

    if ($result instanceof MethodNotAllowed || $result instanceof NotFound) {
        throw $result;
    }

    throw NotFound::create(MissingContextValue::forKey(CtxKey::Result));
}
