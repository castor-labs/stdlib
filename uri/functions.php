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

namespace Castor\Net\Uri;

/**
 * resolvePath applies special path segments from refs and applies
 * them to base, per RFC 3986.
 *
 * @psalm-pure
 */
function resolvePath(string $base, string $ref): string
{
    // TODO: https://cs.opensource.google/go/go/+/refs/tags/go1.19.5:src/net/url/url.go;drc=ba913774543d7388b7bb1843fc7c1b935aebedda;l=995
    throw new \LogicException('Not Implemented');
}
