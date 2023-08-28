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

namespace Castor\Console\Command;

use Castor\Console\Command\Action\ParseError;

/**
 * @implements \IteratorAggregate<int,Arg>
 */
final class ArgList implements \IteratorAggregate
{
    /**
     * @param Arg[] $args
     */
    public function __construct(
        private readonly array $args
    ) {
    }

    public static function new(Arg ...$args): ArgList
    {
        return new self($args);
    }

    /**
     * @param string[] $args
     *
     * @throws ParseError
     */
    public function process(array &$args): void
    {
        $i = 0;
        while ([] !== $args) {
            $peek = $args[0] ?? null;
            if (\str_starts_with($peek, '-')) {
                break;
            }

            $arg = $this->args[$i] ?? null;
            if (!$arg instanceof Arg) {
                throw new ParseError('Too many arguments');
            }

            $value = \array_shift($args) ?? '';
            $arg->consume($value);
            ++$i;
        }
    }

    public function empty(): bool
    {
        return [] === $this->args;
    }

    /**
     * @return \ArrayIterator<int,Arg>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->args);
    }
}
