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

use Brick\DateTime\Clock;
use Brick\DateTime\Instant;
use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;
use Castor\Crypto\Bytes;

use function Castor\Err\must;

class GregorianTime
{
    /**
     * The number of 100-nanosecond intervals from the Gregorian calendar epoch
     * to the Unix epoch.
     */
    private const GREGORIAN_TO_UNIX_OFFSET = '122192928000000000';

    /**
     * The number of 100-nanosecond intervals in one second.
     */
    private const SECOND_INTERVALS = '10000000';

    public function __construct(
        public readonly Bytes $bytes,
    ) {
    }

    public static function fromTimestamp(BigInteger $timestamp): GregorianTime
    {
        return must(static function () use ($timestamp) {
            $hex = \str_pad($timestamp->toBase(16), 16, '0', STR_PAD_LEFT);

            return new self(Bytes::fromHex($hex));
        });
    }

    public static function fromInstant(Instant $instant): GregorianTime
    {
        return must(function () use ($instant) {
            $epochSeconds = BigInteger::of($instant->getEpochSecond());
            $nanoSeconds = BigInteger::of($instant->getNano());

            $secondsTicks = $epochSeconds->multipliedBy(self::SECOND_INTERVALS);
            $nanoTicks = $nanoSeconds->dividedBy(100, RoundingMode::DOWN);
            $ticksSinceEpoch = $secondsTicks->plus($nanoTicks);

            return self::fromTimestamp($ticksSinceEpoch->plus(self::GREGORIAN_TO_UNIX_OFFSET));
        });
    }

    public static function now(Clock $clock): GregorianTime
    {
        return self::fromInstant($clock->getTime());
    }

    public function getInstant(): Instant
    {
        return must(function () {
            $ticksSinceEpoch = $this->getTimestamp()->minus(self::GREGORIAN_TO_UNIX_OFFSET); // Subtract gregorian offset

            $epochSeconds = $ticksSinceEpoch->dividedBy(self::SECOND_INTERVALS, RoundingMode::DOWN);
            $nanoSeconds = $ticksSinceEpoch->remainder(self::SECOND_INTERVALS)->multipliedBy(100);

            return Instant::of($epochSeconds->toInt(), $nanoSeconds->toInt());
        });
    }

    /**
     * Returns the number of 100 nanosecond intervals since 1582-10-15 00:00:00 UTC as a numeric string.
     */
    public function getTimestamp(): BigInteger
    {
        return must(function () {
            return BigInteger::fromBase($this->bytes->toHex(), 16);
        });
    }
}
