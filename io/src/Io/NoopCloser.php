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

namespace Castor\Io;

/**
 * NoopCloser composes a Reader into a ReadCloser.
 */
final class NoopCloser implements Reader, Closer
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getReader(): Reader
    {
        return $this->reader;
    }

    public function close(): void
    {
        if ($this->reader instanceof Closer) {
            $this->reader->close();
        }
    }

    /**
     * @throws EndOfFile
     */
    public function read(int $length): string
    {
        return $this->reader->read($length);
    }
}
