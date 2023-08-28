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

namespace Castor\Console\Command\Action;

use Castor\Console;
use Castor\Console\Command\Action;
use Castor\Console\Command\ArgList;
use Castor\Console\Command\FlagSet;
use Castor\Console\Session;
use Castor\Context;

final class RunCommand implements Action
{
    public function __construct(
        public readonly FlagSet $flags,
        public readonly ArgList $args,
        private Action $action
    ) {
    }

    public function execute(Context $ctx, Session $cli): int
    {
        $args = Console\getArgs($ctx);

        // Before arguments flags
        $this->flags->process($args);
        // Process arguments
        $this->args->process($args);
        // After arguments flags
        $this->flags->process($args);

        // Inject flags
        foreach ($this->flags as $flag) {
            $cli->addFlag($flag);
            if ($flag instanceof Console\Command\Flag\Actionable && $flag->shouldRun()) {
                $this->action = $flag;
            }
        }

        // Inject arguments
        foreach ($this->args as $arg) {
            $cli->addArg($arg);
        }

        return $this->action->execute(Console\withArgs($ctx, $args), $cli);
    }
}
