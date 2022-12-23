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

use Castor\Context;
use Castor\Debug\Logger;
use Castor\Err;
use Castor\Io\Error;
use Castor\Net\Http;
use Castor\Net\Http\Cookie;
use Castor\Net\Http\Cookies;
use Castor\Net\Http\Handler;
use Castor\Net\Http\Headers;
use Castor\Net\Http\Method;
use Castor\Net\Http\Request;
use Castor\Net\Http\Status;
use Castor\Net\Http\Version;
use Castor\Net\Uri;
use Castor\Str;

/**
 * The serve method runs a handler in a CGI context.
 *
 * It parses the request from the available super globals and provides a response
 * writer that handles sending the header and the content back to the client.
 *
 * It also processes uploaded files and keys if the request is of the multipart
 * content type.
 *
 * @throws \Throwable
 */
function serve(Context $ctx, Handler $handler, Logger $logger = null, bool $catchErrors = false): void
{
    $logger = $logger ?? new Logger\Noop();

    if (PHP_SAPI === 'cli') {
        throw new Error('Cannot serve in a non CGI context');
    }

    $request = parseRequest($ctx);
    $writer = ResponseWriter::create();
    $writer->headers()->add('Server', 'PHP CGI');

    try {
        $handler->handle($ctx, $request, $writer);
    } catch (\Throwable $e) {
        if (!$catchErrors) {
            throw $e;
        }

        $ctx = Logger\Meta\withValue($ctx, 'errors', Err\collect($e));
        $logger->log(Logger\Level\error($ctx), 'Uncaught error while handling request');

        if (!$writer->areHeadersSent()) {
            $writer->headers()->set('Content-Type', 'text/plain');
            $writer->writeHeaders(Status::INTERNAL_SERVER_ERROR);
            $writer->write('Error while processing request');
        }
    }

    $writer->flush();
    $request->body->close();
}

/**
 * Returns the parsed body of the request.
 */
function getParsedBody(Context $ctx): array
{
    return $ctx->value(CTX_PARSED_BODY) ?? [];
}

/**
 * Returns the uploaded files.
 *
 * @return UploadedFile[]
 */
function getUploadedFiles(Context $ctx): array
{
    return $ctx->value(CTX_UPLOADED_FILES) ?? [];
}

/**
 * @internal
 */
const CTX_UPLOADED_FILES = 'http.cgi.uploaded_files';

/**
 * @internal
 */
const CTX_PARSED_BODY = 'http.cgi.parsed_body';

/**
 * Parses the Request from the globals.
 *
 * It mutates the passed context
 *
 * @internal
 */
function parseRequest(Context &$ctx): Request
{
    $server = $_SERVER;
    if (!array_key_exists('REQUEST_METHOD', $server)) {
        $server['REQUEST_METHOD'] = 'GET';
    }

    $method = Method::from($server['REQUEST_METHOD'] ?? throw new \RuntimeException('Could not determine HTTP method'));
    $headers = \function_exists('getallheaders') ? Headers::fromMap(getallheaders()) : parseHeaders($server);

    if (Method::POST === $method) {
        $contentType = $headers->get('Content-Type');
        if (in_array(explode(';', $contentType), ['application/x-www-form-urlencoded', 'multipart/form-data'])) {
            $ctx = Context\withValue($ctx, CTX_PARSED_BODY, $_POST);
        }
    }

    $ctx = Http\withParsedCookies($ctx, parseCookies($_COOKIE));

    $uri = parseUri($server);
    $version = Version::from($server['SERVER_PROTOCOL'] ?? 'HTTP/1.1');
    $body = RequestBody::create();

    return new Request($version, $method, $uri, $headers, $body);
}

/**
 * Parses HTTP Headers from the $_SERVER global.
 *
 * @internal
 */
function parseHeaders(array $server = []): Headers
{
    $server = $server ?? $_SERVER;
    $headers = new Headers();
    foreach ($server as $key => $value) {
        if (!$value) {
            continue;
        }

        if (str_starts_with($key, 'HTTP_')) {
            $name = Str\replace(Str\slice($key, 5), '_', '-');
            $headers->set($name, $value);

            continue;
        }

        if (str_starts_with($key, 'CONTENT_')) {
            $name = 'content-'.Str\slice($key, 8);
            $headers->set($name, $value);
        }
    }

    return $headers;
}

/**
 * Parses the URI from the $_SERVER global.
 *
 * @internal
 */
function parseUri(array $server = null): Uri
{
    $server = $server ?? $_SERVER;

    $uri = Uri::fromParts();

    if (isset($server['HTTP_X_FORWARDED_PROTO'])) {
        $uri = $uri->withScheme($server['HTTP_X_FORWARDED_PROTO']);
    } else {
        if (isset($server['REQUEST_SCHEME'])) {
            $uri = $uri->withScheme($server['REQUEST_SCHEME']);
        } elseif (isset($server['HTTPS'])) {
            $uri = $uri->withScheme('on' === $server['HTTPS'] ? 'https' : 'http');
        }

        $port = (string) ($server['SERVER_PORT'] ?? '');
        if ('' !== $port) {
            $uri = $uri->withHost($uri->getHostname().':'.$port);
        }
    }

    $host = $server['HTTP_HOST'] ?? '';
    if ('' !== $host) {
        $uri = $uri->withHost($host);
    }

    $host = $server['SERVER_NAME'] ?? '';
    if ('' !== $host) {
        $uri = $uri->withHost($host);
    }

    if (isset($server['REQUEST_URI'])) {
        $uri = $uri->withPath(\current(\explode('?', $server['REQUEST_URI'])));
    }

    if (isset($server['QUERY_STRING'])) {
        $uri = $uri->withRawQuery($server['QUERY_STRING']);
    }

    return $uri;
}

/**
 * Parses the cookies from the $_COOKIE global.
 *
 * @param null|array<string,string> $cookies
 *
 * @internal
 */
function parseCookies(array $cookies = null): Cookies
{
    $cookies = $cookies ?? $_COOKIE;
    $parsed = [];

    foreach ($cookies as $key => $value) {
        $parsed[] = new Cookie($key, $value);
    }

    return Cookies::create(...$parsed);
}
