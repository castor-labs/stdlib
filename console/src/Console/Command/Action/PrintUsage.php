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
use Castor\Console\Command\Arg;
use Castor\Console\Printer;
use Castor\Console\Session;
use Castor\Context;

final class PrintUsage implements Action
{
    public function __construct(
        private readonly bool $isError,
    ) {
    }

    public static function asError(): PrintUsage
    {
        return new self(true);
    }

    public static function asInfo(): PrintUsage
    {
        return new self(false);
    }

    public function execute(Context $ctx, Session $cli): int
    {
        $exec = Console\getExecutable($ctx);
        $out = $this->isError ? $cli->error : $cli->output;

        if ($exec instanceof Console\App) {
            $out->write('%s (ver. %s)', $exec->name, $exec->version);
        } else {
            $out->write($exec->name);
        }

        $out->write($exec->description);

        if ($exec->action instanceof RunCommand) {
            $this->printCommandUsage($ctx, $out, $exec->action);

            return $this->isError ? 1 : 0;
        }

        return $this->isError ? 1 : 0;
    }

    private function printCommandUsage(Context $ctx, Printer $out, RunCommand $command): void
    {
        if (!$command->args->empty()) {
            $out->write(PHP_EOL.'ARGUMENTS');
            foreach ($command->args as $arg) {
                $out->write('%s (%s) %s%s', $arg->name, $this->getArgType($arg), "\t", $arg->description);
                if ('' !== $arg->explanation) {
                    $out->write($arg->explanation);
                }
            }
        }

        if (!$command->flags->empty()) {
            $out->write(PHP_EOL.'OPTIONS');
            foreach ($command->flags as $flag) {
                $flag->printUsage($out);
            }
        }
    }

    private function getArgType(Arg $arg): string
    {
        return match (true) {
            $arg instanceof Arg\Str => 'string'
        };
    }
}
