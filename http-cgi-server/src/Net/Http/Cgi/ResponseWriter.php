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
 * ResponseWriter wraps the PHP CGI Output stream.
 *
 * It is an implementation detail of the CGI package
 *
 * @internal
 */
final class ResponseWriter extends Io\PhpResource implements IResponseWriter
{
    private Headers $headers;
    private bool $sentHeaders;

    public static function create(): ResponseWriter
    {
        $writer = self::make(fopen('php://output', 'wb'));
        $writer->headers = new Headers();
        $writer->sentHeaders = false;

        return $writer;
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
        if ($this->sentHeaders) {
            throw new Io\Error('Headers already sent');
        }

        foreach ($this->headers as $name => $value) {
            header($name.': '.$value, false, $status);
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

        return parent::write($bytes);
    }
}
