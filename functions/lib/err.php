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

namespace Castor\Err;

use Castor\RegExp;

/**
 * Returns a clean string with the last PHP error.
 */
function getLassErrorClean(): string
{
    $array = \error_get_last();
    $message = $array['message'] ?? 'Unknown Error';
    \error_clear_last();
    $matches = RegExp\matches('/\(\): (.*)/', $message);

    return $matches[1] ?? $message;
}

/**
 * Collects all exceptions in an array.
 *
 * @return array<int,array{type: class-string, message: string, code: int, file: string, line: int}>
 */
function collect(\Throwable $e): array
{
    $errors = [];
    while ($e instanceof \Throwable) {
        $errors[] = [
            'type' => \get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        $e = $e->getPrevious();
    }

    return $errors;
}

/**
 * Wraps a callable that will never fail.
 *
 * This is useful for catching exceptions that will never occur.
 *
 * @template T
 *
 * @param callable():T $fn
 *
 * @return T
 *
 * @throws \RuntimeException if an error is encountered
 */
function must(callable $fn): mixed
{
    try {
        return $fn();
    } catch (\Throwable $e) {
        throw new \RuntimeException('Impossible error', previous: $e);
    }
}
