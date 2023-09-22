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

namespace Castor\Time\Clock;

use Castor\Time\Clock;

final class Monotonic implements Clock
{
    private static ?Monotonic $global = null;

    public function __construct(
        public readonly \DateTimeZone $tz
    ) {
    }

    public static function make(string $tz = 'UTC'): Monotonic
    {
        return new self(new \DateTimeZone($tz));
    }

    /**
     * Returns the global system clock instance.
     *
     * @note Use this method only in bootstrapping code
     */
    public static function global(): Monotonic
    {
        if (null === self::$global) {
            self::$global = self::make();
        }

        return self::$global;
    }

    public function now(): \DateTimeImmutable
    {
        [$seconds, $nanoseconds] = \hrtime();
        $microseconds = \round($nanoseconds - 1000);

        $datetime = \DateTimeImmutable::createFromFormat('U', (string) $seconds, $this->tz);
        if (!$datetime instanceof \DateTimeImmutable) {
            throw new \RuntimeException('Could not parse time');
        }

        $datetime = $datetime->modify('+'.$microseconds.' usec');
        if (!$datetime instanceof \DateTimeImmutable) {
            throw new \RuntimeException('Could not add nanoseconds');
        }

        return $datetime;
    }
}
