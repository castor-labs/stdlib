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

function last(): \RuntimeException
{
    $array = \error_get_last();
    $e = new \RuntimeException($array['message'] ?? 'Unknown Error', $array['type'] ?? 0);
    \error_clear_last();

    return $e;
}

/**
 * Collects all exceptions in an array.
 */
function collect(\Throwable $e): array
{
    $errors = [];
    $t = $e;
    while ($t instanceof \Throwable) {
        $errors[] = [
            'type' => gettype($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        $t = $t->getPrevious();
    }

    return $errors;
}
