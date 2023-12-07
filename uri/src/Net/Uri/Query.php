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

namespace Castor\Net\Uri;

use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<string, string>
 *
 * @psalm-external-mutation-free
 */
class Query implements \IteratorAggregate
{
    /**
     * @var array<string,string[]>
     */
    private array $items;

    public function __construct()
    {
        $this->items = [];
    }

    /**
     * @param array<string,string[]> $items
     */
    public static function create(array $items = []): Query
    {
        $uri = new self();
        foreach ($items as $key => $values) {
            $uri->set($key, ...$values);
        }

        return $uri;
    }

    public static function decode(string $rawQuery): Query
    {
        $query = new self();

        if ('' === $rawQuery) {
            return $query;
        }

        $parts = \explode('&', $rawQuery);
        foreach ($parts as $part) {
            $i = \strpos($part, '=');
            if (!\is_int($i)) {
                $query->items[\urldecode($part)][] = '';

                continue;
            }

            $key = \urldecode(\substr($part, 0, $i));
            $value = \urldecode(\substr($part, $i + 1));
            $query->items[$key][] = $value;
        }

        return $query;
    }

    /**
     * @psalm-mutation-free
     */
    public function get(string $key): string
    {
        return $this->values($key)[0] ?? '';
    }

    /**
     * @psalm-mutation-free
     */
    public function lookup(string $key): ?string
    {
        return $this->values($key)[0] ?? null;
    }

    /**
     * @return array|string[]
     *
     * @psalm-mutation-free
     */
    public function values(string $key): array
    {
        return $this->items[$key] ?? [];
    }

    /**
     * @psalm-mutation-free
     */
    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->items);
    }

    /**
     * @return $this
     */
    public function add(string $key, string $value): Query
    {
        $this->items[$key][] = $value;

        return $this;
    }

    /**
     * @param string[] $values
     */
    public function set(string $key, string ...$values): Query
    {
        $this->items[$key] = $values;

        return $this;
    }

    /**
     * @return $this
     */
    public function del(string $key): Query
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * @psalm-mutation-free
     */
    public function isEmpty(): bool
    {
        return [] === $this->items;
    }

    /**
     * @return array|string[][]
     *
     * @psalm-mutation-free
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return \Iterator<string, string>
     *
     * @psalm-mutation-free
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->items as $key => $values) {
            foreach ($values as $value) {
                yield $key => $value;
            }
        }
    }

    /**
     * @psalm-mutation-free
     */
    public function encode(): string
    {
        $parts = [];
        foreach ($this as $key => $value) {
            if ('' === $value) {
                $parts[] = \urlencode($key);

                continue;
            }

            $parts[] = \urlencode($key).'='.\urlencode($value);
        }

        return \implode('&', $parts);
    }
}
