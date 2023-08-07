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

namespace Castor\Debug\Logger;

use Castor\Arr;
use Castor\Debug\Logger;
use Castor\Io\Flusher;
use Castor\Io\Writer;
use Castor\Os;
use Castor\Str;

final class Standard implements Logger
{
    private const PARAM_APP = '@logger.app';
    private const DEFAULT_APP_NAME = 'system';

    /**
     * Creates a new standard logger.
     */
    public function __construct(
        public Writer $out,
        public Level $minimum,
        public Level $default,
        public Colorizer $colorizer,
        public string $app = self::DEFAULT_APP_NAME
    ) {
    }

    /**
     * Flush any buffered output when the logger is destroyed.
     */
    public function __destruct()
    {
        if ($this->out instanceof Flusher) {
            $this->out->flush();
        }
    }

    public static function default(Writer $writer = null): Standard
    {
        $writer = $writer ?? Os\stderr();

        return new self(
            $writer,
            Level::DEBUG,
            Level::DEBUG,
            new Logger\Colorizer\Ansi(),
        );
    }

    public function log(string $message, mixed ...$params): void
    {
        $level = $this->default;
        $meta = [];

        foreach ($params as $param) {
            if ($param instanceof Level) {
                $level = $param;

                continue;
            }

            if (\is_array($param)) {
                $meta[] = $param;
            }
        }

        // Merge the meta into one
        $meta = Arr\merge(...$meta);

        // App name
        $app = $meta[self::PARAM_APP] ?? $this->app;
        if ('' !== $app) {
            unset($meta[self::PARAM_APP]);
        }

        if ($level->value < $this->minimum->value) {
            return;
        }

        $parts = [Str\format(
            '%s [%s]',
            $this->levelString($level),
            $app
        )];

        $this->interpolate($message, $meta);

        $parts[] = $message;

        $this->normalizeMetadata($level, $parts, $meta);

        $this->out->write(Str\join($parts, ' ').PHP_EOL);
    }

    private function levelString(Level $level): string
    {
        $string = match ($level) {
            Level::FATAL => 'FTAL',
            Level::ERROR => 'ERRO',
            Level::WARN => 'WARN',
            Level::INFO => 'INFO',
            Level::DEBUG => 'DBUG',
            Level::TRACE => 'TRCE',
        };

        return $this->colorizer->colorize($level, $string);
    }

    private function normalizeMetadata(Level $level, array &$kv, array $metadata, string $prefix = ''): void
    {
        foreach ($metadata as $key => $value) {
            $key = $prefix.$key;

            // Skip null and empty string values
            if (null === $value || '' === $value) {
                continue;
            }

            // We cast it to an array if is an object
            if (\is_object($value)) {
                $value = (array) $value;
            }

            if (\is_array($value)) {
                $this->normalizeMetadata($level, $kv, $value, $key.'.');

                return;
            }

            $kv[] = Str\format('%s=%s', $this->colorizer->colorize($level, $key), $value);
        }
    }

    private function interpolate(string &$message, array &$meta): void
    {
        $newMeta = [];
        $replace = [];
        foreach ($meta as $key => $val) {
            $newKey = '{'.$key.'}';
            if (!Str\contains($message, $newKey)) {
                $newMeta[$key] = $val;

                continue;
            }

            if (\is_array($val)) {
                $newMeta[$key] = $val;

                continue;
            }

            if (\is_object($val) && !\method_exists($val, '__toString')) {
                $newMeta[$key] = $val;

                continue;
            }

            $replace[$newKey] = $val;
        }

        $message = \strtr($message, $replace);
        $meta = $newMeta;
    }
}
