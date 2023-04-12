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

function stderr(): Writer&Flusher
{
    static $stream = null;
    if (null === $stream) {
        $stream = Stream::create(STDERR);
    }

    return $stream;
}

function stdout(): Writer&Flusher
{
    static $stream = null;
    if (null === $stream) {
        $stream = Stream::create(STDOUT);
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

function getCwd(): string
{
    $result = getcwd();
    if (false === $result) {
        throw Err\last();
    }

    return $result;
}

function readFile(string $filename): string
{
    $contents = \file_get_contents($filename);
    if (\is_string($contents)) {
        return $contents;
    }

    throw Err\last();
}

function tempDir(): string
{
    return \sys_get_temp_dir();
}

function makeDir(string $path, int $mode = 0755, bool $recursive = true): string
{
    if (!\is_dir($path) && !@\mkdir($path, $mode, $recursive) && !\is_dir($path)) {
        throw Err\last();
    }

    return $path;
}
