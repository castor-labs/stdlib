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
