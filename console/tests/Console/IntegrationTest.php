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
use Castor\Console\Command\Arg;
use Castor\Console\Command\Flag;
use Castor\Io;
use Castor\Time\Clock;
use Castor\Time\Clock\Frozen;
use PHPUnit\Framework\TestCase;

/**
 * This test tests that all the different components work correctly.
 *
 * @internal
 *
 * @coversDefaultClass
 */
class IntegrationTest extends TestCase
{
    public function testNoopApplication(): void
    {
        $out = Io\Stream::memory();
        $error = Io\Stream::memory();

        $app = Console\App::new(
            name: 'Noop',
            version: '1.0.0',
            description: 'An app that does nothing',
            output: $out,
            error: $error
        );

        $code = $app->process(['noop']);

        $this->assertSame(1, $code);
        $this->assertSame('', Io\readAll($out));
        $this->assertSame("Application 'Noop' has no action defined\n", Io\readAll($error));
    }

    /**
     * @dataProvider getGreetingAppData
     */
    public function testGreetingApp(
        int $expectedStatus,
        string $expectedOutput,
        string $expectedError,
        array $arguments,
        Clock $clock,
    ) {
        $out = Io\Stream::memory();
        $error = Io\Stream::memory();

        $app = Console\App::new(
            name: 'Greet',
            version: '1.0.0',
            description: 'An app that greets people',
            args: [
                new Arg\Str(
                    name: 'name',
                    description: 'The name to greet'
                ),
            ],
            flags: [
                new Flag\Boolean(
                    name: 'time',
                    short: 't',
                    description: 'Whether to give a time based greeting',
                ),
            ],
            action: new Action\Func(static function (Session $cli, string $name, bool $time) use ($clock): int {
                if (!$time) {
                    $cli->output->write('Hello, %s!', $name);

                    return 0;
                }

                $hour = (int) $clock->now()->format('H');

                $time = match (true) {
                    $hour < 12 => 'morning',
                    $hour < 17 => 'afternoon',
                    $hour < 20 => 'evening',
                    default => 'night'
                };

                $cli->output->write('Good %s, %s!', $time, $name);

                return 0;
            }),
            output: $out,
            error: $error
        );

        $code = $app->process($arguments);

        $this->assertSame($expectedStatus, $code);
        $this->assertSame($expectedOutput, Io\readAll($out));
        $this->assertSame($expectedError, Io\readAll($error));
    }

    public function getGreetingAppData(): array
    {
        return [
            'no arguments' => [
                1,
                '',
                "No arguments in argument list\n",
                [],
                Frozen::at('H', '22'),
            ],
            'missing required argument' => [
                1,
                '',
                "Argument 'name' is required\n",
                ['greet'],
                Frozen::at('H', '22'),
            ],
            'single argument' => [
                0,
                "Hello, Matias!\n",
                '',
                ['greet', 'Matias'],
                Frozen::at('H', '22'),
            ],
            'too many arguments' => [
                1,
                '',
                "Too many arguments\n",
                ['greet', 'Matias', 'Navarro'],
                Frozen::at('H', '22'),
            ],
            'time flag at front, morning' => [
                0,
                "Good morning, Matias!\n",
                '',
                ['greet', '-t', 'Matias'],
                Frozen::at('H', '08'),
            ],
            'time flag at end, afternoon' => [
                0,
                "Good afternoon, Matias!\n",
                '',
                ['greet', 'Matias', '-t'],
                Frozen::at('H', '15'),
            ],
            'prints help' => [
                0,
                <<<'EOT'
                Greet (ver. 1.0.0)
                An app that greets people
                
                ARGUMENTS
                name (string) 	The name to greet

                OPTIONS
                -t, --time 	Whether to give a time based greeting
                -h, --help 	Prints this help information
                
                EOT,
                '',
                ['greet', '-h'],
                Frozen::at('H', '22'),
            ],
        ];
    }
}
