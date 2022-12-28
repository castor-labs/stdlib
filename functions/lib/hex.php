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

namespace Castor\Encoding\Hex;

use Castor\Encoding\EncodingInputError;
use Castor\Err;

/**
 * @throws EncodingInputError
 *
 * @noinspection PhpDocMissingThrowsInspection
 */
function decode(string $hex): string
{
    $decoded = @\hex2bin($hex);
    if (!is_string($decoded)) {
        // @noinspection PhpUnhandledExceptionInspection
        throw Err\lastReplace('bin2hex(): ', '', EncodingInputError::class);
    }

    return $decoded;
}

function encode(string $bytes): string
{
    return \bin2hex($bytes);
}
