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
use Castor\Console\App;
use Castor\Console\Command\Action;
use Castor\Console\Session;
use Castor\Context;

/**
 * This Action runs when a command is defined with no action.
 *
 * It's just for the internal use of this library. You should not
 * use this class in your own actions.
 *
 * @internal
 */
class Noop implements Action
{
    /**
     * @internal
     */
    public function execute(Context $ctx, Session $cli): int
    {
        $exec = Console\getExecutable($ctx);
        if ($exec instanceof App) {
            throw new ExecutionError("Application '{$exec->name}' has no action defined");
        }

        throw new ExecutionError("Command '{$exec->name}' has no action defined");
    }
}
