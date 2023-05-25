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

/**
 * Returns PHP's last error as an exception.
 *
 * @template T
 *
 * @param class-string<T> $class
 *
 * @return T
 */
function last(string $class = \RuntimeException::class): \Throwable
{
    $array = \error_get_last();
    $e = new $class($array['message'] ?? 'Unknown Error', $array['type'] ?? 0);
    \error_clear_last();

    return $e;
}

/**
 * Returns PHP's last error as an exception but modifying the message.
 *
 * @template T
 *
 * @param class-string<T> $class
 *
 * @return T
 */
function lastReplace(string $substring, string $replacement, string $class = \RuntimeException::class): \Throwable
{
    $array = \error_get_last();
    $message = $array['message'] ?? 'Unknown Error';
    $message = \str_replace($substring, $replacement, $message);
    $e = new $class($message, $array['type'] ?? 0);
    \error_clear_last();

    return $e;
}

/**
 * Collects all exceptions in an array.
 * @return array<int,array{type: class-string, message: string, code: int, file: string, line: int}>
 */
function collect(\Throwable $e): array
{
    $errors = [];
    while ($e instanceof \Throwable) {
        $errors[] = [
            'type' => \gettype($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        $e = $e->getPrevious();
    }

    return $errors;
}
