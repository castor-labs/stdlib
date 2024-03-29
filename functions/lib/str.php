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

namespace Castor\Str;

use Castor\Bytes;

use function ltrim as native_ltrim;
use function rtrim as native_rtrim;
use function trim as native_trim;

const TITLE_SEPARATORS = " \t\r\n\f\v";
const TRIM_CHARS = " \t\n\r\0\x0B";

/**
 * @psalm-pure
 *
 * @deprecated use fmt instead
 */
function format(string $format, mixed ...$values): string
{
    return \sprintf($format, ...$values);
}

/**
 * @psalm-pure
 */
function fmt(string $format, mixed ...$values): string
{
    return \sprintf($format, ...$values);
}

/**
 * @psalm-pure
 */
function toUpper(string $string): string
{
    return \strtoupper($string);
}

/**
 * @psalm-pure
 */
function toLower(string $string): string
{
    return \strtolower($string);
}

/**
 * @psalm-pure
 */
function toTitle(string $string, string $separators = TITLE_SEPARATORS): string
{
    return \ucwords($string, $separators);
}

/**
 * @param string $string  The string to perform the replacements in
 * @param string $search  The string to search
 * @param string $replace The replacement string
 *
 * @psalm-pure
 */
function replace(string $string, string $search, string $replace): string
{
    return \str_replace($search, $replace, $string);
}

/**
 * Returns the index where the first occurrence of a substring starts.
 *
 * Returns -1 if the substring is not found
 *
 * @psalm-pure
 */
function index(string $string, string $substring): int
{
    $pos = \strpos($string, $substring);
    if (!\is_int($pos)) {
        return -1;
    }

    return $pos;
}

/**
 * @psalm-pure
 */
function trim(string $string, string $chars = TRIM_CHARS): string
{
    return native_trim($string, $chars);
}

/**
 * @psalm-pure
 */
function ltrim(string $string, string $chars = TRIM_CHARS): string
{
    return native_ltrim($string, $chars);
}

/**
 * @psalm-pure
 */
function rtrim(string $string, string $chars = TRIM_CHARS): string
{
    return native_rtrim($string, $chars);
}

/**
 * Splits a string into parts by the separator.
 *
 * @return string[]
 *
 * @psalm-pure
 */
function split(string $string, string $separator = ' ', int $limit = null): array
{
    if (null !== $limit) {
        return \explode($separator, $string, $limit);
    }

    return \explode($separator, $string);
}

/**
 * Takes a slice from a string.
 *
 * @psalm-pure
 */
function slice(string $string, int $offset, int $size = 0): string
{
    if (0 === $size) {
        return \substr($string, $offset);
    }

    return \substr($string, $offset, $size);
}

/**
 * @param string[] $array
 *
 * @psalm-pure
 */
function join(array $array, string $glue = ''): string
{
    return \implode($glue, $array);
}

/**
 * Cuts a string in two by the first occurrence of substring.
 *
 * The substring is not included in the result
 *
 * @return array{0: string, 1: string, 2: bool}
 *
 * @psalm-pure
 */
function cut(string $string, string $substring): array
{
    $len = Bytes\len($substring);
    $i = index($string, $substring);

    if ($i < 0) {
        return [$string, '', false];
    }

    return [
        slice($string, 0, $i),
        slice($string, $i + $len),
        true,
    ];
}

/**
 * @psalm-pure
 */
function contains(string $string, string $substring): bool
{
    return \str_contains($string, $substring);
}
