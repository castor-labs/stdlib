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
use Castor\Io\Error;
use Castor\Io\Reader;

use const Castor\Io\SEEK_START;

use Castor\Io\Seeker;
use Castor\Io\Stream;
use Castor\Io\Writer;

/**
 * A Recorder is a ResponseWriter that can be used in test suites.
 *
 * It can produce a Response if the headers have been sent.
 */
final class Recorder implements ResponseWriter
{
    public function __construct(
        private readonly Headers $headers,
        private readonly Reader&Writer&Closer $body,
        private readonly ?Status $status = null,
    ) {
    }

    public static function create(): Recorder
    {
        return new self(
            new Headers(),
            Stream::memory(),
        );
    }

    public function headers(): Headers
    {
        return $this->headers;
    }

    public function writeHeaders(int $status = Status::OK): void
    {
        if (null !== $this->status) {
            throw new Error('Headers and status code has already been sent');
        }
    }

    public function write(string $bytes): int
    {
        if (null === $this->status) {
            $this->writeHeaders();
        }

        return $this->body->write($bytes);
    }

    public function getResponse(): Response
    {
        if (null === $this->status) {
            $this->writeHeaders();
        }

        if ($this->body instanceof Seeker) {
            $this->body->seek(0, SEEK_START);
        }

        return new Response(
            Version::HTTP11,
            $this->status,
            $this->headers,
            $this->body,
        );
    }
}
