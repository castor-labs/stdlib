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
use Castor\Debug\LevelLogger;
use Castor\Debug\Logger;
use Castor\Err;
use Castor\Io\Error;
use Castor\Net\Http;
use Castor\Net\Http\Cookie;
use Castor\Net\Http\Cookies;
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
function serve(Context $ctx, Http\Handler $handler, Logger $logger = null, bool $catchErrors = false, bool $ignoreUserAbort = true): void
{
    $logger = $logger ?? new Logger\Noop();
    $logger = new LevelLogger($logger);

    // This will continue script execution even if the client disconnects
    \ignore_user_abort($ignoreUserAbort);

    // We pass this cancellation signal so the user can check whether the client disconnected
    $ctx = Context\withCancel($ctx, fn () => 1 === \connection_aborted());

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

        $errors = Err\collect($e);
        foreach ($errors as $error) {
            $logger->error($error['message'], [
                'type' => $error['type'],
                'file' => $error['file'],
                'line' => $error['line'],
            ]);
        }

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
 * Parses the Request from the globals.
 *
 * It mutates the passed context
 *
 * @internal
 */
function parseRequest(Context &$ctx): Request
{
    $ctx = Http\withClientIp($ctx, $_SERVER['REMOTE_ADDR']);
    $server = $_SERVER;
    if (!\array_key_exists('REQUEST_METHOD', $server)) {
        $server['REQUEST_METHOD'] = 'GET';
    }

    $method = Method::from($server['REQUEST_METHOD'] ?? throw new \RuntimeException('Could not determine HTTP method'));
    $headers = \function_exists('getallheaders') ? Headers::fromMap(\getallheaders()) : parseHeaders($server);

    $ctx = Http\withParsedCookies($ctx, parseCookies($_COOKIE));
    $ctx = withParsedBody($ctx, $_POST);
    $ctx = withUploadedFiles($ctx, UploadedFile::createFromGlobal($_FILES));

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

        if (\str_starts_with($key, 'HTTP_')) {
            $name = Str\replace(Str\slice($key, 5), '_', '-');
            $headers->set($name, $value);

            continue;
        }

        if (\str_starts_with($key, 'CONTENT_')) {
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

    $uri = Uri::parse($server['REQUEST_URI'] ?? '/');

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

    // If path info is not empty and is different to thr request URI, then path info has preference.
    $pathInfo = \current(\explode('?', $server['PATH_INFO'] ?? ''));
    if ('' !== $pathInfo && $uri->getPath() !== $pathInfo) {
        $uri = $uri->withPath($pathInfo);
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

/**
 * @internal
 */
enum ContextKeys
{
    case PARSED_BODY;

    case UPLOADED_FILES;
}

/**
 * Stores the parsed body in the context.
 *
 * @param array<string,mixed> $body
 *
 * @internal
 */
function withParsedBody(Context $ctx, array $body): Context
{
    return Context\withValue($ctx, ContextKeys::PARSED_BODY, $body);
}

/**
 * Returns the parsed body of the request.
 *
 * It's usually the value of $_POST
 *
 * @return array<string,mixed>
 */
function getParsedBody(Context $ctx): array
{
    return $ctx->value(ContextKeys::PARSED_BODY) ?? [];
}

/**
 * @param array<string,UploadedFile> $files
 *
 * @internal
 */
function withUploadedFiles(Context $ctx, array $files): Context
{
    return Context\withValue($ctx, ContextKeys::UPLOADED_FILES, $files);
}

/**
 * Returns the uploaded files.
 *
 * It's usually the value of $_FILES but parsed into an UploadedFile
 *
 * @return array<string,UploadedFile>
 */
function getUploadedFiles(Context $ctx): array
{
    return $ctx->value(ContextKeys::UPLOADED_FILES) ?? [];
}
