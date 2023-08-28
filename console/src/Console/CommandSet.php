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

namespace Castor\Console;

use Castor\Console\Command\Action\ParseError;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int,Command>
 */
class CommandSet implements \IteratorAggregate
{
    /**
     * @param Command[]         $list
     * @param array<string,int> $index
     */
    protected function __construct(
        private array $list = [],
        private array $index = [],
    ) {
    }

    public static function fromList(Command ...$commands): CommandSet
    {
        $set = new self();
        foreach ($commands as $command) {
            $set->add($command);
        }

        return $set;
    }

    public function add(Command $command): void
    {
        $key = \count($this->list);

        $i = $this->index[$command->name] ?? -1;
        if ($i >= 0) {
            throw new \LogicException("Command with name '{$command->name}' is already registered");
        }

        $this->index[$command->name] = $key;

        foreach ($command->aliases as $alias) {
            $i = $this->index[$alias] ?? -1;
            if ($i >= 0) {
                throw new \LogicException("Alias '{$alias}' for command '{$command->name}' is already registered");
            }

            $this->index[$alias] = $key;
        }

        $this->list[] = $command;
    }

    /**
     * @throws ParseError
     */
    public function get(string $name): Command
    {
        $i = $this->index[$name] ?? -1;
        $command = $this->list[$i] ?? null;
        if (!$command instanceof Command) {
            throw new ParseError("Command '{$name}' does not exist", 127);
        }

        return $command;
    }

    public function has(string $name): bool
    {
        return ($this->index[$name] ?? -1) >= 0;
    }

    /**
     * @return \ArrayIterator<int,Command>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->list);
    }
}
