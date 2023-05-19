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
use Castor\Net\Http\Headers;
use Castor\Net\Http\ResponseWriter as IResponseWriter;

/**
 * ResponseWriter writes to the PHP CGI output.
 *
 * It is an implementation detail of the CGI package
 *
 * @internal
 */
final class ResponseWriter implements IResponseWriter, Io\Flusher, Io\Closer
{
    private function __construct(
        private readonly Headers $headers,
        private bool $sentHeaders = false,
        private bool $closed = false,
    ) {
    }

    public static function create(): ResponseWriter
    {
        return new self(new Headers());
    }

    public function areHeadersSent(): bool
    {
        return $this->sentHeaders;
    }

    public function headers(): Headers
    {
        return $this->headers;
    }

    /**
     * @throws Io\Error when headers have been sent already
     */
    public function writeHeaders(int $status = 200): void
    {
        if ($this->closed) {
            throw new Io\Error('Connection is already closed');
        }

        if ($this->sentHeaders) {
            throw new Io\Error('Headers already sent');
        }

        foreach ($this->headers as $name => $value) {
            \header($name.': '.$value, false, $status);
        }

        $this->sentHeaders = true;
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $bytes): int
    {
        if (false === $this->sentHeaders) {
            $this->writeHeaders();
        }

        if ($this->closed) {
            throw new Io\Error('Connection is already closed');
        }

        $len = \strlen($bytes);
        echo $bytes;

        return $len;
    }

    public function close(): void
    {
        if ($this->closed) {
            return;
        }

        if (\function_exists('fastcgi_finish_request')) {
            \fastcgi_finish_request();
        }
        $this->closed = true;
    }

    public function flush(): void
    {
        if ($this->closed) {
            throw new Io\Error('Connection is already closed');
        }

        \ob_flush();
        \flush();
    }
}
