Castor Console
==============

```php
<?php

use Castor\Console;
use Castor\Console\Arg;
use Castor\Console\Flag;
use Castor\Os;

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
            action: new Action\Func(static function (Session $cli, string $name, bool $time): int {
                if (!$time) {
                    $cli->writeln('Hello, %s!', $name);

                    return 0;
                }

                $hour = (int) date('H');
                
                $time = match (true) {
                    $hour < 12 => 'morning',
                    $hour < 17 => 'afternoon',
                    $hour < 20 => 'evening',
                    default => 'night'
                };

                $cli->writeln('Good %s, %s!', $time, $name);
                return 0;
            }),
        );

$app->process(['greet', 'Matias', '-t']); // Prints: "Good evening, Matias!"
```