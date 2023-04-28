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

namespace Castor\Arr;

/**
 * @psalm-suppress ImpureFunctionCall
 *
 * @see https://github.com/vimeo/psalm/issues/2112
 *
 * @psalm-pure
 */
function map(array $array, \Closure $func): array
{
    return \array_map($func, $array);
}

/**
 * @psalm-suppress ImpureFunctionCall
 *
 * @see https://github.com/vimeo/psalm/issues/2112
 *
 * @psalm-pure
 */
function filter(array $array, \Closure $func = null): array
{
    return \array_filter($array, $func);
}

/**
 * @psalm-pure
 */
function push(array &$array, mixed $element): int
{
    return \array_push($array, $element);
}

/**
 * @psalm-pure
 */
function reverse(array $array): array
{
    return \array_reverse($array);
}

/**
 * @return null|mixed
 */
function shift(array &$array): mixed
{
    return \array_shift($array);
}

/**
 * Creates an array from the values of $array.
 *
 * @return list<mixed>
 *
 * @psalm-pure
 */
function values(array $array): array
{
    return \array_values($array);
}

/**
 * Creates an array from the keys of $array.
 *
 * @return list<array-key>
 *
 * @psalm-pure
 */
function keys(array $array): array
{
    return \array_keys($array);
}

/**
 * Creates an array with $n elements of the given $value.
 *
 * @return list<mixed>
 *
 * @psalm-pure
 */
function fill(int $n, mixed $value): array
{
    return \array_fill(0, $n, $value);
}

/**
 * @psalm-pure
 */
function contains(array $array, mixed $value): bool
{
    return \in_array($value, $array, true);
}
