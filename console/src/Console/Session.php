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
use Castor\Console\Command\Arg;
use Castor\Console\Command\Flag;
use Castor\Io;

class Session
{
    /**
     * @param array<string,Flag> $flags
     * @param array<string,Arg>  $args
     */
    public function __construct(
        public readonly Io\Reader $input,
        public readonly Printer $output,
        public readonly Printer $error,
        private array $flags = [],
        private array $args = [],
    ) {
    }

    public function addFlag(Flag $flag): void
    {
        $this->flags[$flag->name] = $flag;
    }

    /**
     * @throws ParseError
     */
    public function getFlag(string $name): Flag
    {
        $flag = $this->flags[$name] ?? null;
        if (!$flag instanceof Flag) {
            throw new ParseError("Flag named '{$name}' does not exist");
        }

        return $flag;
    }

    public function hasFlag(string $name): bool
    {
        return \array_key_exists($name, $this->flags);
    }

    public function addArg(Arg $arg): void
    {
        $this->args[$arg->name] = $arg;
    }

    /**
     * @throws ParseError
     */
    public function getArg(string $name): Arg
    {
        $arg = $this->args[$name] ?? null;
        if (!$arg instanceof Arg) {
            throw new ParseError("Argument named '{$name}' does not exist");
        }

        return $arg;
    }

    public function hasArg(string $name): bool
    {
        return \array_key_exists($name, $this->args);
    }
}
