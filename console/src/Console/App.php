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

use Castor\Arr;
use Castor\Console;
use Castor\Console\Command\Action;
use Castor\Console\Command\Action\ExecutionError;
use Castor\Console\Command\Action\ParseError;
use Castor\Console\Command\Arg;
use Castor\Console\Command\Flag;
use Castor\Context;
use Castor\Io;
use Castor\Os;

final class App extends Executable
{
    protected function __construct(
        string $name,
        public readonly string $version,
        string $description,
        Action $action,
        public readonly Io\Reader $input,
        public readonly Printer $output,
        public readonly Printer $error,
    ) {
        parent::__construct($name, $description, [], $action);
    }

    /**
     * @param Arg[]     $args
     * @param Flag[]    $flags
     * @param Command[] $commands
     */
    public static function new(
        string $name,
        string $version = 'dev',
        string $description = '',
        array $args = [],
        array $flags = [],
        array $commands = [],
        ?Action $action = null,
        ?Io\Reader $input = null,
        ?Io\Writer $output = null,
        ?Io\Writer $error = null,
        bool $enableHelp = true,
        ?Flag\Actionable $helpFlag = null,
        ?Action $helpAction = null,
        bool $enableVersion = true,
        ?Flag\Actionable $versionFlag = null,
        ?Action $versionAction = null,
    ): App {
        $action = self::parseAction($flags, $args, $commands, $action);

        if ($enableHelp && $action instanceof Action\RunCommand) {
            $action->flags->add($helpFlag ?? new Flag\Actionable(
                name: 'help',
                action: $helpAction ?? Action\PrintUsage::asInfo(),
                short: 'h',
                description: 'Prints this help information',
            ));
        }

        return new self(
            $name,
            $version,
            $description,
            $action,
            $input ?? Os\stdin(),
            new Printer($output ?? Os\stdin()),
            new Printer($error ?? Os\stderr()),
        );
    }

    /**
     * Process the given arguments.
     *
     * @param string[] $args
     */
    public function process(array $args): int
    {
        $ctx = Console\withArgs(Context\nil(), $args);

        $cli = new Session(
            $this->input,
            $this->output,
            $this->error,
        );

        try {
            return $this->execute($ctx, $cli);
        } catch (ParseError $e) {
            $cli->error->write($e->getMessage());

            return $e->getCode() ?? 1;
        } catch (ExecutionError $e) {
            $cli->error->write($e->getMessage());

            return $e->getCode() ?? 126;
        } catch (\Throwable $e) {
            $cli->error->write($e->getMessage());

            return 1;
        }
    }

    public function run(): never
    {
        $args = Os\args();

        exit($this->process($args));
    }

    /**
     * @throws ParseError     if there is an error parsing command arguments
     * @throws ExecutionError if there in an error while executing the command logic
     */
    public function execute(Context $ctx, Session $cli): int
    {
        // Do some argument checks
        $args = Console\getArgs($ctx);
        if ([] === $args) {
            throw new ParseError('No arguments in argument list');
        }

        $binName = Arr\shift($args);

        $ctx = Console\withBinName($ctx, $binName);
        $ctx = Console\withArgs($ctx, $args);

        // We defer to executable to handle the rest
        return parent::execute($ctx, $cli);
    }
}
