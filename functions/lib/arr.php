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

function map(array $array, callable $func): array
{
    return \array_map($func, $array);
}

function filter(array $array, callable $func): array
{
    return \array_filter($array, $func);
}

function push(array &$array, mixed $element): int
{
    return \array_push($array, $element);
}

function reverse(array $array): array
{
    return \array_reverse($array);
}
