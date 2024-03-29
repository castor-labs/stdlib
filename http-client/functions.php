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

namespace Castor\Net\Http;

use Castor\Context;
use Castor\Net\Uri\ParseError;

/**
 * @throws ParseError     if there is an error parsing the Uri
 * @throws TransportError
 */
function get(Context $ctx, string $uri): Response
{
    $request = Request::create(Method::GET->value, $uri);

    return Client::default()->send($ctx, $request);
}
