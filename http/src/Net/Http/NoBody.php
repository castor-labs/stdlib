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
use Castor\Io\EndOfFile;
use Castor\Io\Reader;

/**
 * Represents an HTTP request or response without a body.
 *
 * This ReadCloser does not have an underlying stream. It can only be read
 * once and returns an empty string on the first read.
 *
 * This is the default ReadCloser for manually created Requests and Responses
 */
final class NoBody implements Reader, Closer
{
    private bool $read;

    public function __construct()
    {
        $this->read = false;
    }

    public function close(): void
    {
        // Noop
    }

    public function read(int $length): string
    {
        if ($this->read) {
            throw new EndOfFile('End of file reached');
        }

        return '';
    }
}
