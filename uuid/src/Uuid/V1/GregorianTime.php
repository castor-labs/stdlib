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

namespace Castor\Uuid\V1;

use Castor\Crypto\Bytes;
use Castor\Encoding\InputError;
use Castor\Time\Clock;

class GregorianTime
{
    /**
     * The number of 100-nanosecond intervals from the Gregorian calendar epoch
     * to the Unix epoch.
     */
    private const GREGORIAN_TO_UNIX_INTERVALS = '122192928000000000';

    /**
     * The number of 100-nanosecond intervals in one second.
     */
    private const SECOND_INTERVALS = '10000000';

    /**
     * The number of 100-nanosecond intervals in one microsecond.
     */
    private const MICROSECOND_INTERVALS = '10';

    public function __construct(
        public readonly Bytes $bytes,
    ) {
    }

    /**
     * @throws InputError
     */
    public static function fromTimestamp(string $timestamp): GregorianTime
    {
        $hex = \str_pad(\base_convert($timestamp, 10, 16), 16, '0', STR_PAD_LEFT);

        return new self(Bytes::fromHex($hex));
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function fromDatetime(\DateTimeImmutable $datetime): GregorianTime
    {
        $seconds = $datetime->format('U');
        $microseconds = $datetime->format('u');

        $nano1 = \bcmul($seconds, self::SECOND_INTERVALS, 0);
        $nano2 = \bcmul($microseconds, self::MICROSECOND_INTERVALS, 0);

        $sum = \bcadd($nano1, $nano2, 0); // Combine both 100-nano second intervals
        $timestamp = \bcadd($sum, self::GREGORIAN_TO_UNIX_INTERVALS, 0); // Add the gregorian count

        return self::fromTimestamp($timestamp);
    }

    public static function now(Clock $clock): GregorianTime
    {
        return self::fromDatetime($clock->now());
    }

    public function getDatetime(): \DateTimeImmutable
    {
        $epocNanoseconds = \bcsub($this->getTimestamp(), self::GREGORIAN_TO_UNIX_INTERVALS, 0); // Subtract gregorian count
        $seconds = \bcdiv($epocNanoseconds, self::SECOND_INTERVALS, 6);
        if (!\str_contains($seconds, '.')) {
            $seconds .= '.0';
        }

        $datetime = \DateTimeImmutable::createFromFormat('U.u', $seconds);
        if (!$datetime instanceof \DateTimeImmutable) {
            throw new \RuntimeException('This error should never happen');
        }

        return $datetime;
    }

    /**
     * Returns the number of 100 nanosecond intervals since 1582-10-15 00:00:00 UTC as a numeric string.
     */
    public function getTimestamp(): string
    {
        return \base_convert($this->bytes->toHex(), 16, 10);
    }
}
