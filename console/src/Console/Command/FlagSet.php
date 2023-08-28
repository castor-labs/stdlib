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
use Castor\Console\Command\Flag\Boolean;
use Castor\Str;

/**
 * @implements \IteratorAggregate<int,Flag>
 */
final class FlagSet implements \IteratorAggregate
{
    /**
     * @param Flag[]            $list
     * @param array<string,int> $index
     */
    private function __construct(
        private array $list = [],
        private array $index = [],
    ) {
    }

    public static function fromList(Flag ...$flags): FlagSet
    {
        $set = new self();
        foreach ($flags as $flag) {
            $set->add($flag);
        }

        return $set;
    }

    public function add(Flag $flag): void
    {
        $key = \count($this->list);

        $i = $this->index[$flag->name] ?? -1;
        if ($i >= 0) {
            throw new \LogicException("Flag with name '{$flag->name}' is already registered");
        }
        $this->index[$flag->name] = $key;

        if ('' === $flag->short) {
            $this->list[$key] = $flag;

            return;
        }

        $i = $this->index[$flag->short] ?? -1;
        if ($i >= 0) {
            throw new \LogicException("Short name '{$flag->short}' for flag '{$flag->name}' is already registered");
        }

        $this->index[$flag->short] = $key;

        $this->list[$key] = $flag;
    }

    /**
     * @throws ParseError
     */
    public function get(string $name): Flag
    {
        $i = $this->index[$name] ?? -1;
        $flag = $this->list[$i] ?? null;

        if (!$flag instanceof Flag) {
            throw new ParseError("Flag '{$name}' does not exist");
        }

        return $flag;
    }

    public function empty(): bool
    {
        return [] === $this->list;
    }

    /**
     * @param string[] $args
     *
     * @throws ParseError
     */
    public function process(array &$args): void
    {
        while ([] !== $args) {
            $peek = $args[0] ?? null;
            if (!\str_starts_with($peek, '-')) {
                break;
            }

            $next = \array_shift($args);
            $flag = Str\ltrim($next, '-');
            [$flag, $value, $ok] = Str\cut($flag, '=');

            $flag = $this->get($flag);

            // We request the next value if we are not dealing with a boolean flag
            if (!$ok && (!$flag instanceof Boolean)) {
                $value = \array_shift($args) ?? '';
            }

            $flag->consume($value);
        }
    }

    /**
     * @return \ArrayIterator<int,Flag>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->list);
    }
}
