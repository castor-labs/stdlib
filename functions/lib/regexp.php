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

namespace Castor\RegExp;

use Castor\Err;

class Error extends \LogicException
{
}

/**
 * @throws Error if the regular expression is invalid
 */
function matches(string $pattern, string $subject): array
{
    $matches = [];
    $result = \preg_match($pattern, $subject, $matches);
    if (false === $result) {
        throw new Error(Err\getLassErrorClean());
    }

    return $matches;
}
