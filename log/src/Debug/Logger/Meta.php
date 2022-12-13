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

namespace Castor\Debug\Logger;

use Castor\Context;

/**
 * This class holds the logger metadata.
 *
 * You should not interact with this class directly, but instead use the provided context functions
 *
 * @internal
 */
class Meta
{
    /**
     * @var array<string,mixed>
     */
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    public function isEmpty(): bool
    {
        return [] === $this->data;
    }

    /**
     * @param array<string,mixed> $values
     */
    public function merge(array $values): void
    {
        $this->data = array_merge_recursive($this->data, $values);
    }

    public function add(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }
}
