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

namespace Castor\Os;

use Castor\Err;
use Castor\Io\Flusher;
use Castor\Io\Reader;
use Castor\Io\Stream;
use Castor\Io\Writer;

use function getcwd as php_getcwd;

function stderr(): Writer&Flusher
{
    static $stream = null;
    if (null === $stream) {
        $stream = Stream::create(\fopen('php://stderr', 'wb'));
    }

    return $stream;
}

function stdout(): Writer&Flusher
{
    static $stream = null;
    if (null === $stream) {
        $stream = Stream::create(\fopen('php://stdout', 'wb'));
    }

    return $stream;
}

function stdin(): Reader
{
    static $stream = null;
    if (null === $stream) {
        $stream = Stream::create(STDIN);
    }

    return $stream;
}

function lookupEnv(string $env): ?string
{
    $string = \getenv($env);
    if (\is_string($string)) {
        return $string;
    }

    return null;
}

function getEnv(string $env): string
{
    return lookupEnv($env) ?? '';
}

/**
 * @return string[]
 */
function args(): array
{
    global $argv;

    return $argv;
}

function getCwd(): string
{
    $result = php_getcwd();
    if (false === $result) {
        throw new \RuntimeException(Err\getLassErrorClean());
    }

    return $result;
}

function readFile(string $filename): string
{
    $contents = \file_get_contents($filename);
    if (\is_string($contents)) {
        return $contents;
    }

    throw new \RuntimeException(Err\getLassErrorClean());
}

function tempDir(): string
{
    return \sys_get_temp_dir();
}

function makeDir(string $path, int $mode = 0755, bool $recursive = true): string
{
    if (!\is_dir($path) && !@\mkdir($path, $mode, $recursive) && !\is_dir($path)) {
        throw new \RuntimeException(Err\getLassErrorClean());
    }

    return $path;
}
