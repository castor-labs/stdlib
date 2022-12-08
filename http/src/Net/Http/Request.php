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

use Castor\Io\Closer;
use Castor\Io\Reader;
use Castor\Net\Uri;
use Castor\Net\Uri\ParseError;

class Request
{
    public Version $version;
    public Method $method;
    public Uri $uri;
    public Headers $headers;
    public Reader&Closer $body;

    /**
     * Creates a Request with the proper components.
     */
    public function __construct(Version $version, Method $method, Uri $uri, Headers $headers, Reader&Closer $body)
    {
        $this->version = $version;
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Creates an HTTP Request from primitive values.
     *
     * @throws ParseError
     */
    public static function create(string $method, string $uri, Reader&Closer $body = new NoBody()): Request
    {
        return new self(Version::HTTP11, Method::from($method), Uri::parse($uri), new Headers(), $body);
    }
}
