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
use Castor\Console\Session;
use Castor\Context;

/**
 * Represents an action that is lazy loaded.
 *
 * Lazy loaded actions are extremely useful to avoid instantiating all of your actions with their
 * dependencies if you have a fairly large command line application.
 *
 * This especial action will only instantiate the action if the command is actually invoked by the program,
 * providing less memory consumption and faster execution times.
 *
 * Lazy actions use a Action\Loader to load actions.
 *
 * @see Action\Loader
 */
final class LazyLoaded implements Action
{
    private ?Action $loaded = null;

    public function __construct(
        private readonly Loader $loader,
        private readonly string $actionName
    ) {
    }

    public function execute(Context $ctx, Session $cli): int
    {
        return $this->loadAction($ctx)->execute($ctx, $cli);
    }

    private function loadAction(Context $ctx): Action
    {
        $exec = Console\getExecutable($ctx);

        if (null === $this->loaded) {
            try {
                $this->loaded = $this->loader->load($this->actionName);
            } catch (Loader\UnexpectedError|Loader\NotFoundError $e) {
                throw new ExecutionError("Could not load action for command {$exec->name}", previous: $e);
            }
        }

        return $this->loaded;
    }
}
