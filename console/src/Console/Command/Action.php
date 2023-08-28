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

use Castor\Console\Command\Action\ExecutionError;
use Castor\Console\Command\Action\ParseError;
use Castor\Console\Session;
use Castor\Context;

interface Action
{
    public const SUCCESS = 1;

    /**
     * Executes a command.
     *
     * @throws ParseError     if there is an error parsing command arguments
     * @throws ExecutionError if there in an error while executing the command logic
     */
    public function execute(Context $ctx, Session $cli): int;
}
