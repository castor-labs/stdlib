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

namespace Castor\Encoding\Json;

/**
 * @throws \JsonException
 */
function decode(string $json): array
{
    return \json_decode($json, true, 512, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_IGNORE);
}

/**
 * @throws \JsonException
 */
function encodePretty(mixed $json): string
{
    return \json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

/**
 * @throws \JsonException
 */
function encode(mixed $json): string
{
    return \json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
}
