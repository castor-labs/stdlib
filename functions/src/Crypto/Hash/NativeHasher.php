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

namespace Castor\Crypto\Hash;

use Castor\Arr;
use Castor\Bytes;

final class NativeHasher implements Hasher
{
    private string $algo;
    private string $buffer;

    private function __construct(string $algo, string $buffer = '')
    {
        $this->algo = $algo;
        $this->buffer = $buffer;
    }

    public static function make(string $algo, string $buffer = ''): NativeHasher
    {
        if (!Arr\contains(\hash_algos(), $algo)) {
            throw new \InvalidArgumentException(\sprintf('Algorithm "%s" is not available', $algo));
        }

        return new self($algo, $buffer);
    }

    public function hash(string $bytes = ''): string
    {
        $this->write($bytes);

        return \hash($this->algo, $this->buffer, true);
    }

    public function hashHex(string $bytes = ''): string
    {
        $this->write($bytes);

        return \hash($this->algo, $this->buffer);
    }

    public function write(string $bytes): int
    {
        $this->buffer .= $bytes;

        return Bytes\len($bytes);
    }

    public function reset(): void
    {
        $this->buffer = '';
    }
}
