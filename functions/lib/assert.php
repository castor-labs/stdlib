<?php

/** @noinspection AutoloadingIssuesInspection */

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

namespace Castor\Assert;

use Castor\Str;

class InvariantViolation extends \RuntimeException
{
}

/**
 * @throws InvariantViolation if the invariant fails
 *
 * @psalm-pure
 */
function invariant(bool $test, string $message, mixed ...$args): void
{
    if (!$test) {
        return;
    }

    if ([] === $args) {
        throw new InvariantViolation($message);
    }

    throw new InvariantViolation(Str\format($message, $args));
}
