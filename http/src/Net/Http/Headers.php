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

use Castor\Io\Writer;
use Castor\Io\WriterTo;
use Castor\Str;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<string,string>
 */
class Headers implements \IteratorAggregate, WriterTo
{
    /**
     * @var array<string,string[]>
     */
    private array $headers;

    public function __construct()
    {
        $this->headers = [];
    }

    /**
     * @param array<string,string> $map
     */
    public static function fromMap(array $map): Headers
    {
        $header = new self();
        foreach ($map as $key => $value) {
            $header->set($key, $value);
        }

        return $header;
    }

    /**
     * Adds a value to $key.
     */
    public function add(string $key, string $value): void
    {
        $this->headers[self::canonize($key)][] = $value;
    }

    /**
     * Sets the value of $key.
     *
     * This overrides previously added values of $key
     */
    public function set(string $key, string $value): void
    {
        $this->headers[self::canonize($key)] = [$value];
    }

    /**
     * Returns the first value of $key.
     *
     * If no value found, it returns an empty string
     */
    public function get(string $key): string
    {
        return $this->lookup($key) ?? '';
    }

    /**
     * Returns the first value of $key.
     *
     * If no value is found, it returns null
     */
    public function lookup(string $key): ?string
    {
        return $this->headers[self::canonize($key)][0] ?? null;
    }

    /**
     * @return string[]
     */
    public function values(string $key): array
    {
        return $this->headers[self::canonize($key)] ?? [];
    }

    public function del(string $key): void
    {
        unset($this->headers[self::canonize($key)]);
    }

    /**
     * Creates a copy of the headers.
     */
    public function copy(): Headers
    {
        $copy = new self();
        $copy->headers = $this->headers;

        return $copy;
    }

    /**
     * Writes the headers in wire format to the passed writer.
     */
    public function writeTo(Writer $writer): int
    {
        $written = 0;
        foreach ($this as $key => $value) {
            $written += $writer->write(sprintf('%s: %s%s', $key, $value, "\n"));
        }

        // After the headers there is always an extra line for the body.
        $written += $writer->write("\n");

        return $written;
    }

    /**
     * Returns a generator that iterates over every header value.
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->headers as $header => $values) {
            foreach ($values as $value) {
                yield $header => $value;
            }
        }
    }

    private static function canonize(string $key): string
    {
        $key = Str\toLower($key);
        $key = Str\replace($key, ' ', '');

        return ucwords($key, '-');
    }
}
