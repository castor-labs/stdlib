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

namespace Castor\Security;

use Castor\Context;
use Castor\MissingContextValue;

/** @internal  */
enum CtxKey
{
    /** @internal  */
    case Principal;
}

function withPrincipal(Context $ctx, Principal $principal): Context
{
    return Context\withValue($ctx, CtxKey::Principal, $principal);
}

function getPrincipal(Context $ctx): Principal
{
    return $ctx->value(CtxKey::Principal) ??
        throw MissingContextValue::forKey(CtxKey::Principal);
}

function lookupPrincipal(Context $ctx): ?Principal
{
    return $ctx->value(CtxKey::Principal);
}

function getIdentity(Context $ctx): Identity
{
    return getPrincipal($ctx)->getIdentity();
}

function lookupIdentity(Context $ctx): ?Identity
{
    return lookupPrincipal($ctx)?->getIdentity();
}
