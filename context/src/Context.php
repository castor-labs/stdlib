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

namespace Castor;

/**
 * Context is an abstraction for retrieving request-scoped state.
 *
 * Historically, whenever has been necessary to pass request scoped values to another method down the call stack, two
 * approaches have been traditionally used.
 *
 * The first one, specially prominent in older PHP codebase, is to rely on global shared state. There are variations
 * of this approach, but at the end they all rely on shared state: you store something in a particular stack frame, and
 * then you can retrieve it later. Shared state can have undesirable side effects, specially if the execution
 * context of the PHP code changes.
 *
 * A second approach has been to pass that information explicitly on any method calls on the call stack until we
 * reach the frame that needs the value. But this has the potential to pollute the call stack as more values need to
 * be passed and, depending on the implementation, it can violate the Law of Demeter.
 *
 * The context abstraction serves as a carrier for all these short-lived values. You only need to pollute your call
 * stack with one extra argument, and you can add as many things as you like to it, making this approach explicit,
 * flexible and safe.
 */
interface Context
{
    /**
     * Returns the value stored in the context for the given key.
     *
     * If the key is not found, `null` MUST be returned.
     *
     * @psalm-pure
     */
    public function value(mixed $key): mixed;
}
