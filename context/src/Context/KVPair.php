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

namespace Castor\Context;

use Castor\Context;

/**
 * KVPair composes a Context holding a key value pair.
 *
 * If the stored key is strictly equal to the passed key, then the stored value is returned.
 *
 * If the keys are not strictly equal, it passes the call to the next context.
 *
 * @psalm-immutable
 *
 * @internal
 */
final class KVPair implements Context
{
    private Context $next;
    private mixed $key;
    private mixed $value;

    public function __construct(Context $next, mixed $key, mixed $value)
    {
        $this->next = $next;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Returns an array with all the key values stored in the
     * context chain, as long as is an interrupted chain of
     * KVPair instances.
     *
     * This function should be used for debugging purposes only.
     *
     * @return array<array{0: mixed, 1: mixed}>
     */
    public static function debug(Context $ctx): array
    {
        $chain = [];
        while ($ctx instanceof self) {
            $chain[] = [$ctx->key, $ctx->value];
            $ctx = $ctx->next;
        }

        return $chain;
    }

    /**
     * {@inheritDoc}
     */
    public function value(mixed $key): mixed
    {
        if ($this->key === $key) {
            return $this->value;
        }

        return $this->next->value($key);
    }
}
