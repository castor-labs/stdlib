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

use Castor\Arr;
use Castor\Context;
use Castor\Err;
use Castor\Io\Error;
use Castor\Net\Http\Cookie;
use Castor\Net\Http\Handler;
use Castor\Net\Http\Headers;
use Castor\Net\Http\Method;
use Castor\Net\Http\Request;
use Castor\Net\Http\Status;
use Castor\Net\Http\Version;
use Castor\Net\Uri;
use Castor\Str;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
function serve(Context $ctx, Handler $handler, LoggerInterface $logger = null, bool $catchErrors = false): void
{
    $logger = $logger ?? new NullLogger();

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

        $logger->error('Uncaught error while handling request', [
            'errors' => Err\collect($e),
        ]);

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
    return $ctx->value(PARSED_BODY_KEY) ?? [];
}

/**
 * Returns the uploaded files.
 *
 * @return UploadedFile[]
 */
function getUploadedFiles(Context $ctx): array
{
    return $ctx->value(UPLOADED_FILES_KEY) ?? [];
}

/**
 * Returns the parsed cookies.
 *
 * @return Cookie[]
 */
function getParsedCookies(Context $ctx): array
{
    return $ctx->value(PARSED_COOKIES_KEY) ?? [];
}

/**
 * @internal
 */
const UPLOADED_FILES_KEY = 'http.cgi.uploaded_files';

/**
 * @internal
 */
const PARSED_BODY_KEY = 'http.cgi.parsed_body';

/**
 * @internal
 */
const PARSED_COOKIES_KEY = 'http.cgi.parsed_cookies';

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
            $ctx = Context\withValue($ctx, PARSED_BODY_KEY, $_POST);
        }
    }

    $ctx = Context\withValue($ctx, PARSED_COOKIES_KEY, Arr\map($_COOKIE, static function (string $value, string $key) {
        return new Cookie($key, $value);
    }));

    $uri = parseUri($server);
    $version = Version::from($server['SERVER_PROTOCOL'] ?? 'HTTP/1.1');
    $body = RequestBody::create();

    return new Request($version, $method, $uri, $headers, $body);
}

/**
 * Creates HTTP headers from the SERVER global.
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

        // TODO: Evaluate if we need this on Nginx
        if (str_starts_with($key, 'CONTENT_')) {
            $name = 'content-'.Str\slice($key, 8);
            $headers->set($name, $value);
        }
    }

    return $headers;
}

/**
 * Parses the URI from the SERVER global.
 *
 * @internal
 */
function parseUri(array $server = []): Uri
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
