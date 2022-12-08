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

namespace Castor\Net\Http\Cgi;

use Castor\Io;

/**
 * RequestBody wraps the PHP CGI input stream.
 *
 * It is an implementation detail of the CGI Server
 *
 * @internal
 */
final class RequestBody extends Io\PhpStream
{
    public static function create(): RequestBody
    {
        return self::make(fopen('php://input', 'rb'));
    }
}
