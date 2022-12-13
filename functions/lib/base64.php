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

namespace Castor\Encoding\Base64\Std;

function encode(string $string): string
{
    return \base64_encode($string);
}

function decode(string $base64): string
{
    return \base64_decode($base64);
}

namespace Castor\Encoding\Base64\Url;
