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

use Castor\Context;
use Castor\Debug\Logger;
use Castor\Io\Flusher;
use Castor\Io\Writer;
use Castor\Os;
use Castor\Str;
use Castor\Time\Clock;

final class Standard implements Logger
{
    /**
     * Creates a new standard logger.
     */
    public function __construct(
        public Writer $out,
        public Level $minimum,
        public Level $default,
        public Colorizer $colorizer,
        public Timer $timer,
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

    public static function default(Writer $writer = null, Clock $clock = null): Standard
    {
        $writer = $writer ?? Os\stderr();
        $clock = $clock ?? Clock\System::global();

        return new self(
            $writer,
            Level::DEBUG,
            Level::DEBUG,
            new Logger\Colorizer\Ansi(),
            new Logger\Timer\Unix($clock),
        );
    }

    public function log(Context $ctx, string $message, mixed ...$params): void
    {
        $level = Logger\getLevel($ctx) ?? $this->default;
        $app = Logger\getApp($ctx) ?? '';

        if ($level->value < $this->minimum->value) {
            return;
        }

        $header = Str\format(
            '%s[%s]',
            $this->levelString($level),
            $this->timer->time(),
        );

        $parts = [$header];
        if ('' !== $app) {
            $parts[] = '['.$app.']';
        }

        $parts[] = $message;

        $this->normalizeMetadata($level, $parts, Logger\getMeta($ctx));

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
}
