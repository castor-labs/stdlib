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

use Castor\Io;
use Castor\Io\Closer;
use Castor\Io\Reader;
use Castor\Io\Writer;
use Castor\Io\WriterTo;

class Response implements WriterTo
{
    public Version $version;
    public Status $status;
    public Headers $headers;
    public Reader&Closer $body;

    protected function __construct(Version $version, Status $status, Headers $headers, Reader&Closer $body)
    {
        $this->version = $version;
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function create(int $status = 200, Reader&Closer $body = new NoBody()): Response
    {
        return new self(Version::HTTP11, Status::fromInt($status), new Headers(), $body);
    }

    public function writeTo(Writer $writer): int
    {
        $written = 0;
        $written += $writer->write(sprintf(
            '%s %s %s%s',
            $this->version->value,
            $this->status->value,
            $this->status->phrase,
            "\n"
        ));

        $written += $this->headers->writeTo($writer);

        $written += Io\copy($this->body, $writer);

        return $written;
    }
}
