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

use Castor\Console;
use Castor\Console\Command\Action;
use Castor\Console\Command\ArgList;
use Castor\Console\Command\FlagSet;
use Castor\Context;

/**
 * Executable forms the base of any command or application.
 */
abstract class Executable implements Action
{
    /**
     * @param string[] $aliases
     */
    protected function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $aliases,
        public readonly Action $action,
    ) {
    }

    public function execute(Context $ctx, Session $cli): int
    {
        $ctx = Console\withExecutable($ctx, $this);

        return $this->action->execute($ctx, $cli);
    }

    /**
     * @param Console\Command\Flag[] $flags
     * @param Console\Command\Arg[]  $args
     * @param Command[]              $commands
     */
    protected static function parseAction(
        array $flags = [],
        array $args = [],
        array $commands = [],
        ?Action $action = null
    ): Action {
        if ([] !== $commands) {
            return new Action\RunSubCommands(
                FlagSet::fromList(...$flags),
                CommandSet::fromList(...$commands)
            );
        }

        return new Action\RunCommand(
            FlagSet::fromList(...$flags),
            ArgList::new(...$args),
            $action ?? new Action\Noop(),
        );
    }
}
